<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Cookie;
	use RawadyMario\Classes\Core\Database;
use RawadyMario\Classes\Core\ShippingProvider\ShippingProviderInterface;
use RawadyMario\Classes\Core\ShippingProvider\TheCourierGuy\Models\QuoteContentsModel;
	use RawadyMario\Classes\Core\ShippingProvider\TheCourierGuy\TheCourierGuy;
	use RawadyMario\Classes\Database\Views\VProduct;
	use RawadyMario\Classes\Database\Views\VProductOption;
	use RawadyMario\Classes\Helpers\Helper;

	class Cart extends Database {
		const ProductTypeCookie = 'cookie';
		const ProductTypeDatabase = 'database';

		private static $cartArr = "";

		public $cartGroups;

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "cart";
			$this->_key		= "id";
			
			$this->_deleteIsAFlag = false;
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}

			$this->cartGroups = [];
		}


		public function _setByUniqueKeys(array $row=[]) {
			$cartGroupId = Helper::ConvertToInt($row["cart_group_id"]);
			$productId = Helper::ConvertToInt($row["product_id"]);
			$optionIds = $row["option_ids"];

			if (!isset($this->list[$cartGroupId])) {
				$this->list[$cartGroupId] = [];
			}
			if (!isset($this->list[$cartGroupId][$productId])) {
				$this->list[$cartGroupId][$productId] = [];
			}
			if (!isset($this->list[$cartGroupId][$productId][$optionIds])) {
				$this->list[$cartGroupId][$productId][$optionIds] = [];
			}
			$this->list[$cartGroupId][$productId][$optionIds] = $row;

			return parent::_set($row);
		}


		public function loadByCartGroupId(int $cartGroupId, array $params=[]) : void {
			$condition = "`e`.`cart_group_id` = $cartGroupId";
			$join = "";
			$select = "";
			$function = $params["function"] ?? "_set";

			$this->listAll($condition, $join, $select, $function);
		}


		public function loadByUserId(int $userId=0, array $params=[]) : void {
			$cartGroup = new CartGroup();
			$cartGroup->loadByUserId($userId);

			if ($cartGroup->count > 0) {
				$this->cartGroups = $cartGroup->data;
				$this->loadByCartGroupId($cartGroup->row["id"], $params);
			}
		}
		
		
		public static function getCartCount() : int {
			$count = 0;

			if (!IS_LOGGED) {
				$cartArr = self::GetCookie();
				$count = Helper::ConvertToInt($cartArr["count"] ?? 0);
			}
			else {
				$cart = new Cart();
				$cart->loadByUserId(LOGGED_ID);
				
				$count = Helper::ConvertToInt($cart->count ?? 0);
			}

			return $count;
		}


		public static function getFull() : array {
			$retArr = self::GetDataToFix();
			$retArr = self::FixCartData($retArr);

			return $retArr;
		}


		private static function GetDataToFix() {
			$retArr = [];

			if (!IS_LOGGED) {
				$cartArr = self::GetCookie();
				$addressId = $cartArr["address_id"] ?? 0;
				$cartData = $cartArr["data"] ?? [];
				foreach ($cartData AS $cartRow) {
					foreach ($cartRow AS $cartProduct) {
						$retArr[] = [
							"type" => self::ProductTypeCookie,
							"id" => 0,
							"cart_group_id" => 0,
							"cart_group" => [
								"shipping_address" => $addressId,
								"shipping_breakdown" => $cartArr["shipping_breakdown"] ?? ""
							],
							"product_id" => $cartProduct["product_id"] ?? 0,
							"option_ids" => $cartProduct["option_ids"] ?? "",
							"quantity" => $cartProduct["quantity"] ?? 1,
						];
					}
				}
			}
			else {
				$cart = new Cart();
				$cart->loadByUserId(LOGGED_ID);
				foreach ($cart->data AS $cartRow) {
					$retArr[] = [
						"type" => self::ProductTypeDatabase,
						"id" => $cartRow["id"] ?? 0,
						"cart_group_id" => $cartRow["cart_group_id"] ?? 0,
						"cart_group" => $cart->cartGroups[0] ?? [],
						"product_id" => $cartRow["product_id"] ?? 0,
						"option_ids" => $cartRow["option_ids"] ?? "",
						"quantity" => $cartRow["quantity"] ?? 1,
					];
				}
			}

			return $retArr;
		}

		
		public static function CopyFromCookieToUser() : bool {
			if (!IS_LOGGED) {
				return false;
			}
			
			$cartArr = self::GetCookie();
			$cartCount = Helper::ConvertToInt($cartArr["count"] ?? 0);
			$cartData = $cartArr["data"] ?? [];
			
			if ($cartCount > 0) {
				foreach ($cartData AS $productOpts) {
					foreach ($productOpts AS $productOpt) {
						$productOpt["option_ids"] = explode(",", $productOpt["option_ids"]);
						$optRet = self::AddToUser($productOpt);

						if ($optRet["code"] !== HTTP_OK) {
							return false;
						}
					}
				}
			}

			return true;
		}


		public static function AddItem(array $arr) : array {
			if (IS_LOGGED) {
				$retArr = self::AddToUser($arr);
			}
			else {
				$retArr = self::AddToCookie($arr);
			}
			return $retArr;
		}


		private static function AddToCookie(array $arr) : array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];
			
			$cartArr = self::GetCookie();
			if (count($cartArr) == 0) {
				$cartArr = [
					"count" => 0,
					"data" => [],
					"shipping_breakdown" => ""
				];
			}
			$cartArr["data"] = self::AddCookieItem($arr, $cartArr["data"] ?? []);
			$cartArr["count"] = count($cartArr["data"]);
			self::SetCookie($cartArr);

			$retArr["code"] = HTTP_OK;
			$retArr["msg"] = "ProductAddedToCart";
			$retArr["response"]["cart_count"] = Helper::ConvertToInt($cartArr["count"] ?? 0);
			
			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"] ?? HTTP_INTERNALERROR);
			$retArr["msg"] = Helper::CleanHtmlText($retArr["msg"] ?? "UnknownErrorOccurred");

			return $retArr;
		}


		private static function AddCookieItem(array $row, array $items=[]) : array {
			$productId = Helper::ConvertToInt($row["product_id"] ?? 0);
			$optionIdsArr = $row["option_ids"] ?? [];
			$optionIdsStr = count($optionIdsArr) > 0 ? implode(",", $optionIdsArr) : "";
			$row["option_ids"] = $optionIdsStr;
			
			if (!isset($items[$productId])) {
				$items[$productId] = [];
			}
			if (!isset($items[$productId][$optionIdsStr])) {
				$items[$productId][$optionIdsStr] = [];
			}
			$items[$productId][$optionIdsStr] = $row;

			return $items;
		}


		private static function AddToUser(array $arr) : array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$userId = Helper::ConvertToInt($arr["user_id"] ?? LOGGED_ID);
			$productId = Helper::ConvertToInt($arr["product_id"] ?? 0);
			$addressId = Helper::ConvertToInt($arr["address_id"] ?? 0);
			$quantity = Helper::ConvertToInt($arr["quantity"] ?? 0);
			$optionIdsArr = $arr["option_ids"] ?? [];
			$optionIdsStr = count($optionIdsArr) == 0 ? "" : implode(",", $optionIdsArr);

			$cartGroup = new CartGroup();
			$cartGroup->loadByUserId($userId);
			if ($cartGroup->count == 0) {
				$cartGroup->insert([
					"user_id" => $userId,
					"store_id" => DEFAULT_STORE_ID
				]);
				$cartGroup->loadByUserId($userId);
			}
			if ($addressId > 0) {
				$cartGroup->update([
					"shipping_address" => $addressId
				]);
			}
			$cartGroupId = $cartGroup->row["id"];

			$params = [
				"cart_group_id" => $cartGroupId,
				"product_id" => $productId,
				"option_ids" => $optionIdsStr,
				"quantity" => $quantity,
			];

			$cart = new Cart();
			$cart->loadByCartGroupId($cartGroupId, [
				"function" => "_setByUniqueKeys"
			]);
			if (!isset($cart->list[$cartGroupId][$productId][$optionIdsStr])) {
				$cart->insert($params);
			}
			else {
				$cartId = $cart->list[$cartGroupId][$productId][$optionIdsStr]["id"];
				$cart->update($params, "`id` = $cartId");
			}

			if (!$cart->error) {
				$retArr["code"] = HTTP_OK;
				$retArr["msg"] = "ProductAddedToCart";
				$retArr["response"]["cart_count"] = Helper::ConvertToInt(self::getCartCount());
			}

			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"] ?? HTTP_INTERNALERROR);
			$retArr["msg"] = Helper::CleanHtmlText($retArr["msg"] ?? "UnknownErrorOccurred");

			return $retArr;
		}


		public static function UpdateItems(array $arr, int $addressId=0): array {
			if (IS_LOGGED) {
				$retArr = self::UpdateUserItems($arr, $addressId);
			}
			else {
				$retArr = self::UpdateCookieItems($arr, $addressId);
			}
			
			return $retArr;
		}


		private static function UpdateCookieItems(array $arr, int $addressId=0) : array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$cookieArr = [
				"count" => 0,
				"address_id" => $addressId,
				"data" => []
			];
			foreach ($arr AS $row) {
				$_productId = Helper::ConvertToInt($row["productId"] ?? 0);
				$_qty = Helper::ConvertToInt($row["qty"] ?? 0);
				$_optionsIdsStr = $row["optionsIds"] ?? "";
				$_optionsIdsArr = explode(",", $_optionsIdsStr);
				
				$cookieArr["count"]++;
				$cookieArr["data"] = self::AddCookieItem([
					"product_id" => $_productId,
					"quantity" => $_qty,
					"option_ids" => $_optionsIdsArr,
				], $cookieArr["data"]);
			}
			self::SetCookie($cookieArr);
			
			$retArr["code"] = HTTP_OK;
			$retArr["msg"] = "CartUpdatedSuccessfully";
			$retArr["response"]["cart_count"] = Helper::ConvertToInt($cookieArr["count"]);
			
			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"] ?? HTTP_INTERNALERROR);
			$retArr["msg"] = Helper::CleanHtmlText($retArr["msg"] ?? "UnknownErrorOccurred");

			return $retArr;
		}


		private static function UpdateUserItems(array $arr, int $addressId) : array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];
			
			foreach ($arr AS $row) {
				$_productId = Helper::ConvertToInt($row["productId"] ?? 0);
				$_qty = Helper::ConvertToInt($row["qty"] ?? 0);
				$_optionsIdsStr = $row["optionsIds"] ?? "";
				$_optionsIdsArr = explode(",", $_optionsIdsStr);

				self::AddToUser([
					"user_id" => LOGGED_ID,
					"product_id" => $_productId,
					"quantity" => $_qty,
					"option_ids" => $_optionsIdsArr,
					"address_id" => $addressId
				]);
			}

			$retArr["code"] = HTTP_OK;
			$retArr["msg"] = "CartUpdatedSuccessfully";
			$retArr["response"]["cart_count"] = Helper::ConvertToInt(self::getCartCount());
			
			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"] ?? HTTP_INTERNALERROR);
			$retArr["msg"] = Helper::CleanHtmlText($retArr["msg"] ?? "UnknownErrorOccurred");

			return $retArr;
		}


		public static function DeleteItem(array $arr) : array {
			if (IS_LOGGED) {
				$retArr = self::DeleteFromUser($arr);
			}
			else {
				$retArr = self::DeleteFromCookie($arr);
			}
			
			return $retArr;
		}


		private static function DeleteFromCookie(array $arr) : array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$productId = Helper::ConvertToInt($arr["product_id"] ?? 0);
			$optionIds = $arr["option_ids"] ?? "";

			$cartArr = self::GetCookie();
			if (count($cartArr) > 0) {
				$count = Helper::ConvertToInt($cartArr["count"] ?? 0);

				if (isset($cartArr["data"][$productId][$optionIds])) {
					unset($cartArr["data"][$productId][$optionIds]);
					if ($count > 0) {
						$count -= 1;
					}
				}
				$cartArr["count"] = $count;
			}
			
			self::SetCookie($cartArr);

			$retArr["code"] = HTTP_OK;
			$retArr["msg"] = Helper::CleanHtmlText("ProductAddedToCart");
			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"] ?? HTTP_INTERNALERROR);
			$retArr["response"]["cart_count"] = Helper::ConvertToInt($cartArr["count"]);

			return $retArr;
		}


		private static function DeleteFromUser(array $arr) : array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$cartId = Helper::ConvertToInt($arr["cart_id"] ?? 0);

			$cart = new self($cartId);

			if ($cart->count === 0) {
				$retArr["code"] = HTTP_NOTFOUND;
				$retArr["msg"] = "ProductNotFound";
				return $retArr;
			}
			
			$cartGroup = new CartGroup($cart->row["cart_group_id"]);
			if ($cartGroup->count === 0) {
				$retArr["code"] = HTTP_NOTFOUND;
				$retArr["msg"] = "ProductNotFound";
				return $retArr;
			}
			if (Helper::ConvertToInt($cartGroup->row["user_id"]) !== LOGGED_ID) {
				$retArr["code"] = HTTP_UNAUTHORIZED;
				$retArr["msg"] = "NoPrivilegeToPerformAction";
				return $retArr;
			}
			
			$cart->delete();

			if (!$cart->error) {
				$retArr["code"] = HTTP_OK;
				$retArr["msg"] = "ProductRemovedFromCart";
				$retArr["response"]["cart_count"] = Helper::ConvertToInt(self::getCartCount());
			}

			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"] ?? HTTP_INTERNALERROR);
			$retArr["msg"] = Helper::CleanHtmlText($retArr["msg"] ?? "UnknownErrorOccurred");

			return $retArr;
		}


		public static function UpdateShippingBreakdown(array $arr, int $cartGroupId=0): array {
			if (IS_LOGGED) {
				$retArr = self::UpdateUserShippingBreakdown($arr, $cartGroupId);
			}
			else {
				$retArr = self::UpdateCookieShippingBreakdown($arr, $cartGroupId);
			}
			return $retArr;
		}


		public static function UpdateUserShippingBreakdown(array $arr, int $cartGroupId=0): array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$cartGroup = new CartGroup($cartGroupId);
			if ($cartGroup->count === 0) {
				$retArr["code"] = HTTP_NOTFOUND;
				$retArr["msg"] = "ProductNotFound";
				return $retArr;
			}
			if (Helper::ConvertToInt($cartGroup->row["user_id"]) !== LOGGED_ID) {
				$retArr["code"] = HTTP_UNAUTHORIZED;
				$retArr["msg"] = "NoPrivilegeToPerformAction";
				return $retArr;
			}
			
			$cartGroup->update([
				"shipping_breakdown" => json_encode($arr)
			]);

			if (!$cartGroup->error) {
				$retArr["code"] = HTTP_OK;
				$retArr["msg"] = "ShippingUpdated";
			}

			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"] ?? HTTP_INTERNALERROR);
			$retArr["msg"] = Helper::CleanHtmlText($retArr["msg"] ?? "UnknownErrorOccurred");

			return $retArr;
		}

		
		public static function UpdateCookieShippingBreakdown(array $arr, int $cartGroupId=0): array {
			$retArr = [];

			$cartArr = self::GetCookie();
			$cartArr["shipping_breakdown"] = json_encode($arr);
			self::SetCookie($cartArr);
			
			return $retArr;
		}


		private static function GetCookie() : array {
			if (Helper::StringNullOrEmpty(self::$cartArr)) {
				$cookie = new Cookie("cart");
				self::$cartArr = $cookie->GetCookie();
			}

			return Helper::StringNullOrEmpty(self::$cartArr) ? [] : json_decode(self::$cartArr, true);
		}


		private static function SetCookie(array $arr) : void {
			$cookie = new Cookie("cart");
			$cookie->SetTime(time() + (60 * 60 * 24 * 365 * 10)); //10 Years
			$cookie->SetCookie(json_encode($arr));

			self::$cartArr = json_encode($arr);
		}


		public static function ClearCookie() : void {
			$cookie = new Cookie("cart");
			$cookie->ClearCookie();

			self::$cartArr = "";
		}


		public static function GetGuestCheckoutCookie() : array {
			$cookie = new Cookie("guest_checkout");
			$data = $cookie->GetCookie();

			return Helper::StringNullOrEmpty($data) ? [] : json_decode($data, true);
		}


		private static function SetGuestCheckoutCookie(array $arr) : void {
			$cookie = new Cookie("guest_checkout");
			$cookie->SetTime(time() + (60 * 60 * 24 * 365 * 10)); //10 Years
			$cookie->SetCookie(json_encode($arr));
		}


		public static function ClearGuestCheckoutCookie() : void {
			$cookie = new Cookie("guest_checkout");
			$cookie->ClearCookie();
		}


		private static function FixCartData(array $arr) : array {
			$retArr = [
				"data" => [], //Cart Data by Group
				"address_id" => 0,
				"breakdown" => [ //Cart Breakdown
					"retail" => [
						"amount" => 0,
					],
					"subtotal" => [
						"amount" => 0,
						"title" => _text("Subtotal"),
						"value" => Helper::ConvertToDec(0, 2, true, CURRENCY_SIGN)
					],
					"subtotalbeforediscount" => [
						"amount" => 0,
						"title" => _text("SubtotalBeforeDiscount"),
						"value" => Helper::ConvertToDec(0, 2, true, CURRENCY_SIGN)
					],
					// "discount" => [
					// 	"amount" => 0,
					// 	"title" => _text("Discount"),
					// 	"value" => Helper::ConvertToDec(0, 2, true, CURRENCY_SIGN)
					// ],
					"subtotalafterdiscount" => [
						"amount" => 0,
						"title" => _text("SubtotalAfterDiscount"),
						"value" => Helper::ConvertToDec(0, 2, true, CURRENCY_SIGN)
					],
					"shipping" => [
						"amount" => 0,
						"title" => _text("Shipping"),
						"value" => _text("N/A")
					],
					// "coupon" => [
					// 	"amount" => 0,
					// 	"title" => _text("Coupon"),
					// 	"value" => ""
					// ],
					"total" => [
						"amount" => 0,
						"title" => _text("Total"),
						"value" => Helper::ConvertToDec(0, 2, true, CURRENCY_SIGN)
					],
				]
			];

			$productIdsArr = [];
			$optionsIdsArr = [];
			foreach ($arr AS $row) {
				$_productId = $row["product_id"] ?? 0;
				$_optionIdsStr = $row["option_ids"] ?? "";
				$_optionIdsArr = $_optionIdsStr != "" ? explode(",", $_optionIdsStr) : [];

				if (!in_array($_productId, $productIdsArr)) {
					$productIdsArr[] = $_productId;
				}
				foreach ($_optionIdsArr AS $_optionId) {
					if (!in_array($_optionId, $optionsIdsArr)) {
						$optionsIdsArr[] = $_optionId;
					}
				}
			}

			$productIdsStr = implode(",", $productIdsArr);
			$optionsIdsStr = implode(",", $optionsIdsArr);

			$products = new VProduct();
			$products->forWebsite();
			$products->listAll("`e`.`id` IN ($productIdsStr)", "", "", "_setByKey");
			
			$options = new VProductOption();
			$options->forWebsite();
			$options->listAll("`e`.`id` IN ($optionsIdsStr)", "", "", "_setByKey");

			foreach ($arr AS $row) {
				if (count($row) == 0) {
					continue;
				}
				
				$type = $row["type"] ?? "";
				$cartId = Helper::ConvertToInt($row["id"] ?? 0);
				$cartGroupId = Helper::ConvertToInt($row["cart_group_id"] ?? 0);
				$cartGroup = $row["cart_group"] ?? [];
				$productId = Helper::ConvertToInt($row["product_id"] ?? 0);
				$optionIds = $row["option_ids"] ?? "";
				$quantity = Helper::ConvertToInt($row["quantity"] ?? 0);

				if (!isset($retArr["data"][$cartGroupId])) {
					$retArr["data"][$cartGroupId] = [
						"products" => [], //Aray of Products, Options, Qty...
					];
				}

				$_productRow = $products->data[$productId] ?? [];
				unset($_productRow["id"]);

				$stockQty = Helper::ConvertToInt($_productRow["final_stock_quantity"] ?? 0);
				$cartUpdate	= [];
				if ($quantity == 0) {
					$quantity = $cartUpdate["quantity"] = 1;
				}
				if ($quantity > $stockQty) {
					$quantity = $cartUpdate["quantity"] = $stockQty;
				}
				if (count($cartUpdate) > 0) {
					$cart = new Cart($cartId);
					$cart->update($cartUpdate);
				}

				$_productRow = array_merge([
					"type" => $type,
					"cart_id" => $cartId,
					"cart_group_id" => $cartGroupId,
					"cart_group" => $cartGroup,
					"product_id" => $productId,
					"option_ids" => $optionIds,
					"quantity" => $quantity,
					"options" => [],
				], $_productRow);

				if ($optionIds != "") {
					$_optionIdsArr = explode(",", $optionIds);
					foreach ($_optionIdsArr AS $_optionId) {
						$_productRow["options"][$_optionId] = $options->data[$_optionId];
					}
				}

				if (!isset($retArr["data"][$cartGroupId]["products"][$productId])) {
					$retArr["data"][$cartGroupId]["products"][$productId] = [];
				}

				$_productRow = Product::FixSingleProductElem($_productRow, true);
				$retArr["data"][$cartGroupId]["products"][$productId][$optionIds] = $_productRow;
				if ($retArr["address_id"] === 0) {
					$retArr["address_id"] = Helper::ConvertToInt($cartGroup["shipping_address"] ?? 0);
				}
				
				$retailPrice = Helper::ConvertToDec($_productRow["final_retail_price"] ?? 0);
				$cartTotalPriceBeforeDiscount = Helper::ConvertToDec($_productRow["cart_total_price_before_discount"] ?? 0);
				$cartTotalPriceAfterDiscount = Helper::ConvertToDec($_productRow["cart_total_price_after_discount"] ?? 0);
				$shippingAmount = 0;

				$retArr["breakdown"]["subtotal"]["amount"] += $cartTotalPriceAfterDiscount;
				$retArr["breakdown"]["subtotalbeforediscount"]["amount"] += $cartTotalPriceBeforeDiscount;
				$retArr["breakdown"]["subtotalafterdiscount"]["amount"] += $cartTotalPriceAfterDiscount;
				$retArr["breakdown"]["shipping"]["amount"] += $shippingAmount;
				$retArr["breakdown"]["total"]["amount"] += $cartTotalPriceAfterDiscount + $shippingAmount;
				$retArr["breakdown"]["retail"]["amount"] += $retailPrice;
			}

			$shippingBreakdown = $arr[0]["cart_group"]["shipping_breakdown"] ?? "";
			if ($shippingBreakdown === "") {
				$quote = self::GetShippingBreakdown(true);
			}
			else {
				$quote = json_decode($shippingBreakdown, true);
			}
			
			$shippingAmount = Helper::ConvertToDec($quote["total"] ?? 0);
			$retArr["breakdown"]["shipping"]["amount"] += $shippingAmount;
			$retArr["breakdown"]["total"]["amount"] += $shippingAmount;

			$retArr["breakdown"]["subtotal"]["value"] = Helper::ConvertToDec($retArr["breakdown"]["subtotal"]["amount"], 2, true, CURRENCY_SIGN);
			$retArr["breakdown"]["subtotalbeforediscount"]["value"] = Helper::ConvertToDec($retArr["breakdown"]["subtotalbeforediscount"]["amount"], 2, true, CURRENCY_SIGN);
			$retArr["breakdown"]["subtotalafterdiscount"]["value"] = Helper::ConvertToDec($retArr["breakdown"]["subtotalafterdiscount"]["amount"], 2, true, CURRENCY_SIGN);
			$retArr["breakdown"]["total"]["value"] = Helper::ConvertToDec($retArr["breakdown"]["total"]["amount"], 2, true, CURRENCY_SIGN);
			if ($retArr["breakdown"]["shipping"]["amount"] > 0) {
				$retArr["breakdown"]["shipping"]["value"] = Helper::ConvertToDec($retArr["breakdown"]["shipping"]["amount"], 2, true, CURRENCY_SIGN);
			}

			if ($retArr["breakdown"]["subtotalbeforediscount"]["amount"] === $retArr["breakdown"]["subtotalafterdiscount"]["amount"]) {
				unset($retArr["breakdown"]["subtotalbeforediscount"]);
				unset($retArr["breakdown"]["discount"]);
				unset($retArr["breakdown"]["subtotalafterdiscount"]);
			}
			else {
				// $retArr["breakdown"]["discount"]["amount"] = Helper::ConvertToDec($retArr["breakdown"]["subtotalbeforediscount"]["amount"]) - Helper::ConvertToDec($retArr["breakdown"]["subtotalafterdiscount"]["amount"]);
				// $retArr["breakdown"]["discount"]["value"] = Helper::ConvertToDec($retArr["breakdown"]["discount"]["amount"], 2, true, CURRENCY_SIGN_DEFAULT);
				unset($retArr["breakdown"]["subtotal"]);
			}

			return $retArr;
		}


		public static function Checkout(): array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$arr = self::getFull();

			$addressId = Helper::ConvertToInt($arr["address_id"] ?? 0);
			$address = new Address($addressId);

			if ($address->count === 0) {
				$retArr["msg"] = "CheckoutAddressError";
				return $retArr;
			}
			
			$retArr["code"] = HTTP_OK;
			$retArr["status"] = STATUS_CODE_SUCCESS;
			$retArr["msg"] = "";
			$retArr["response"] = $arr;
			
			return $retArr;
		}


		private static function GetShippingQuoteFromApi(array $arr): array {
			$addresId = Helper::ConvertToInt($arr[0]["cart_group"]["shipping_address"] ?? 0);
			$addressRet = Address::LoadAddress($addresId);
			$address = $addressRet["code"] === HTTP_OK ? json_decode(json_encode($addressRet["response"]["address"]), true) : [];

			$shipping = new TheCourierGuy();

			$shipping->toPostCode = $address["zipcode"] ?? "";
			$shipping->toTownName = $address["city"] ?? "";

			$shipping->detailsModel->instruction = "Quote from Cart";
			$shipping->detailsModel->reference = "Quote from Cart";

			$shipping->detailsModel->toAddressLine1 = $address["address"] ?? "";
			$shipping->detailsModel->toAddressLine2 = $address["city"] ?? "";
			$shipping->detailsModel->toAddressLine3 = $address["state"] ?? "";
			$shipping->detailsModel->toPhoneNumber = Helper::ImplodeArrStr([$address["landline_code"] ?? "", $address["landline"] ?? ""], "");
			$shipping->detailsModel->toMobileNumber = Helper::ImplodeArrStr([$address["mobile_code"] ?? "", $address["mobile"] ?? ""], "");
			$shipping->detailsModel->fromPerson = $address["full_name"] ?? "";
			$shipping->detailsModel->fromContactName = $address["full_name"] ?? "";

			$shippingModels = [];

			$productIdsArr = [];
			foreach ($arr AS $row) {
				$_productId = $row["product_id"] ?? 0;
				
				if (!in_array($_productId, $productIdsArr)) {
					$productIdsArr[] = $_productId;
				}
			}

			$productIdsStr = implode(",", $productIdsArr);
			
			$products = new VProduct();
			$products->forWebsite();
			$products->listAll("`e`.`id` IN ($productIdsStr)", "", "", "_setByKey");
			
			$i = 1;
			foreach ($arr AS $row) {
				if (count($row) == 0) {
					continue;
				}
				
				$type = $row["type"] ?? "";
				$cartId = Helper::ConvertToInt($row["id"] ?? 0);
				$cartGroupId = Helper::ConvertToInt($row["cart_group_id"] ?? 0);
				$cartGroup = $row["cart_group"] ?? [];
				$productId = Helper::ConvertToInt($row["product_id"] ?? 0);
				$optionIds = $row["option_ids"] ?? "";
				$quantity = Helper::ConvertToInt($row["quantity"] ?? 0);

				$_productRow = $products->data[$productId] ?? [];
				unset($_productRow["id"]);

				$_productRow = array_merge([
					"type" => $type,
					"cart_id" => $cartId,
					"cart_group_id" => $cartGroupId,
					"cart_group" => $cartGroup,
					"product_id" => $productId,
					"option_ids" => $optionIds,
					"quantity" => $quantity,
					"options" => [],
				], $_productRow);
				$_productRow = Product::FixSingleProductElem($_productRow, true);

				$shippingModel = new QuoteContentsModel();
				$shippingModel->index = $i;
				$shippingModel->id = $productId;
				$shippingModel->SetName($_productRow["name"] ?? "");
				$shippingModel->count = Helper::ConvertToInt($_productRow["quantity"] ?? 0);
				$shippingModel->width = Helper::ConvertToInt($_productRow["width"] ?? 0);
				$shippingModel->length = Helper::ConvertToInt($_productRow["length"] ?? 0);
				$shippingModel->height = Helper::ConvertToInt($_productRow["height"] ?? 0);
				$shippingModel->weight = Helper::ConvertToDec(($_productRow["weight"] ?? 0) * 0.001, 6);
				$shippingModels[] = $shippingModel;

				$i++;
			}

			/**
			 * @var QuoteContentsModel $shippingModel
			 */
			foreach ($shippingModels AS $shippingModel) {
				$shipping->AddContentModel($shippingModel);
			}
			
			return $shipping->RequestQuote();
		}


		public static function GetShippingBreakdown(bool $update=false): array {
			$fixedArr = self::GetDataToFix();
			if (count($fixedArr) === 0) {
				return [];
			}
			$quote = [
				"total" => ShippingProviderInterface::MINIMUM_SHIPPING_AMOUNT
			];
			// $quote = self::GetShippingQuoteFromApi($fixedArr);
			// if (count($quote) > 0) {
			// 	if (isset($quote["total"]) && Helper::ConvertToDec($quote["total"]) < ShippingProviderInterface::MINIMUM_SHIPPING_AMOUNT) {
			// 		$quote["total"] = ShippingProviderInterface::MINIMUM_SHIPPING_AMOUNT;
			// 	}
			// }
			
			if ($update && count($quote) > 0) {
				$cartGroupId = Helper::ConvertToInt($fixedArr[0]["cart_group_id"] ?? 0);
				self::UpdateShippingBreakdown($quote, $cartGroupId);
			}

			return $quote;
		}


		public static function GuestCheckout(array $arr=[]): array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];
			
			if (count($arr) > 0) {
				self::SetGuestCheckoutCookie($arr);

				$retArr["code"] = HTTP_OK;
				$retArr["msg"] = "";
			}
			else {
				self::ClearGuestCheckoutCookie($arr);
			}
			
			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"] ?? HTTP_INTERNALERROR);
			$retArr["msg"] = Helper::CleanHtmlText($retArr["msg"] ?? "UnknownErrorOccurred");

			return $retArr;
		}

	}

?>