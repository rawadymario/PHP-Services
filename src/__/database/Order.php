<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Core\OnlinePayment\PayFast\PayFast;
	use RawadyMario\Classes\Helpers\DateHelper;
	use RawadyMario\Classes\Helpers\Helper;
	use RawadyMario\Classes\Submit\AuthFormSubmit;

	class Order extends Database {
		
		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "order";
			$this->_key		= "id";
			
			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}
		}

		public function update($values=null, $condition="", $join="", $saveHistory=true) {
			if (!$values) {
				$values = $this->row;
			}

			if ($saveHistory && isset($values["order_status"])) {
				$orderId = Helper::ConvertToInt($this->row[$this->_key]);
				$savedOrder = new self($orderId);
				
				$oldOrderStatus = Helper::ConvertToInt($savedOrder->row["order_status"]);
				$newOrderStatus = Helper::ConvertToInt($values["order_status"]);

				if ($newOrderStatus !== $oldOrderStatus) {
					$orderStatusHistory = new OrderStatusHistory();
					$orderStatusHistory->insert([
						"order_id" => $orderId,
						"category_id" => VARIABLE_CAT_ORDER_STATUS,
						"old_status" => $oldOrderStatus,
						"new_status" => $newOrderStatus,
					]);
				}
			}

			return parent::update($values, $condition, $join);
		}

		public function cancel() {
			$orderProducts = new OrderProduct();
			$orderProducts->loadByOrder($this->getKeyValue());
			
			foreach ($orderProducts->data AS $row) {
				$productId = $row["product_id"];
				$optionIds = $row["option_ids"];
				$qty = Helper::ConvertToInt($row["qty"]);

				if ($optionIds != "") {
					$optionIdsArr = explode(",", $optionIds);
					
					foreach ($optionIdsArr AS $optionId) {
						$productOption = new ProductOption($optionId);
						$productOption->update([
							"stock_quantity"	=> $productOption->row["stock_quantity"] + $qty
						], "`id` = $optionId");
					}
				}

				$product = new Product($productId);
				$product->update([
					"stock_quantity" => $product->row["stock_quantity"] + $qty
				], "`id` = $productId");
			}

			$this->update([
				"order_status" => ORDER_STATUS_CANCELED
			], "`id` = " . $this->getKeyValue());
		}

		public function refund() {
			$this->update([
				"payment_status" => PAYMENT_STATUS_REFUNDED
			], "`id` = " . $this->getKeyValue());
		}

		public function loadByInvoiceNb($invoiceNb="") {
			parent::listAll("e.`invoice_nb` = '$invoiceNb'");
		}

		public function loadByOnlinePaymentInvoiceId($id="") {
			parent::listAll("e.`online_payment_invoice_id` = '$id'");
		}

		public function loadByOnlinePaymentPaymentId($id="") {
			parent::listAll("e.`online_payment_payment_id` = '$id'");
		}

		public function GetPayableAmount() {
			return Helper::ConvertToDec($this->row["products_amount"] + $this->row["shipping_amount"]);
		}

		public function UpdateOrderAfterPayment(string $paymentStatus): void {
			switch ($paymentStatus) {
				case PayFast::STATUS_SUCCESS:
					$updateArr = [
						"order_status" => ORDER_STATUS_ORDERED,
						"payment_status" => PAYMENT_STATUS_PAID,
						"payment_method" => PAYMENT_METHOD_ONLINE,
					];
					break;
				
				case PayFast::STATUS_ERROR:
					$updateArr = [
						"order_status" => ORDER_STATUS_ERROR_IN_ONLINE_PAYMENT,
						"payment_status" => PAYMENT_STATUS_UNPAID,
						"payment_method" => PAYMENT_METHOD_ONLINE,
					];
					break;
			}
			$this->update($updateArr);
		}

		public function SendOrderStatusUpdatedEmails(): array {
			$orderId = Helper::ConvertToInt($this->row["id"] ?? 0);
			$orderStatusHistory = OrderStatusHistory::GetLatest($orderId);
			
			if (count($orderStatusHistory) > 0) {
				$queue = new Queue();
				$queue->name = "SendOrderStatusUpdatedEmailToAdmin";
				$queue->payload = json_encode([
					"order_id" => $orderId,
					"old_status_id" => $orderStatusHistory["old_status"],
					"new_status_id" => $orderStatusHistory["new_status"],
				]);
				$retArr = $queue->SendEmail(false);
				
				if ($retArr["status"] !== SUCCESS) {
					return $retArr;
				}
				
				$queue = new Queue();
				$queue->name = "SendOrderStatusUpdatedEmailToUser";
				$queue->payload = json_encode([
					"order_id" => $orderId,
					"old_status_id" => $orderStatusHistory["old_status"],
					"new_status_id" => $orderStatusHistory["new_status"],
				]);
				return $queue->SendEmail(false);
			}

			return [
                "status" => ERROR,
                "message" => "OrderNotFound"
            ];
		}

		public function isForUser(
			int $userId=0
		): bool {
			return $this->row["user_id"] == $userId;
		}

		public function isForStore(
			int $userId=0
		): bool {
			// $stores = new Store();
			// $stores->listStores($userId);

			// $storeIdsArr = [];
			// foreach ($stores->data AS $row) {
			// 	$storeIdsArr[] = $row["id"];
			// }
			
			// return in_array($this->row["store_id"], $storeIdsArr);
			return true;
		}

		public function isForLogged(
			int $userId=0
		): bool {
			return ($this->isForUser($userId)) || ((IS_STORE || IS_ADMIN) && $this->isForStore($userId)) || IS_DEV || IS_SUPER;
		}


		/**
		 * =================
		 * Static Functions:
		 * =================
		 */
		public static function generateInvoiceNb(
			int $userId=0
		): string {
			$sql = "SELECT COUNT(*) + 1 AS `NewCount` FROM `order` e WHERE e.`user_id` = $userId";

			$o	= new self();
			$o->listAllAdvanced($sql);

			$newCount = Helper::ConvertToInt($o->row["NewCount"]);
			unset($o);

			return "order_" . sprintf("%05d", $userId) . "_" . sprintf("%010d", $newCount) . "_" . rand(1000, 9999);
		}

		public static function storeChangePaymentTo(
			array $row
		): array {
			$retArr	= [];

			if ($row["payment_status"] == PAYMENT_STATUS_UNPAID && $row["payment_method"] == PAYMENT_METHOD_COD) {
				switch ($row["order_status"]) {
					case ORDER_STATUS_CANCELED:
						$retArr[]	= PAYMENT_STATUS_UNPAID;
						$retArr[]	= PAYMENT_STATUS_REFUNDED;
						break;

					case ORDER_STATUS_ORDERED:
					case ORDER_STATUS_PROCESSING:
					case ORDER_STATUS_SHIPPING_IN_PROGRESS:
					case ORDER_STATUS_SHIPPED:
					case ORDER_STATUS_DELIVERED:
						$retArr[]	= PAYMENT_STATUS_UNPAID;
						$retArr[]	= PAYMENT_STATUS_PAID;
						break;
				}
			}

			if ($row["payment_status"] == PAYMENT_STATUS_PAID && $row["order_status"] == ORDER_STATUS_CANCELED) {
				$retArr[] = PAYMENT_STATUS_PAID;
				$retArr[] = PAYMENT_STATUS_REFUNDED;
			}

			return $retArr;
		}

		public static function storeChangeOrderTo(
			array $row
		): array {
			$retArr	= [];

			switch ($row["order_status"]) {
				case ORDER_STATUS_ORDERED:
					$retArr[]	= ORDER_STATUS_ORDERED;
					$retArr[]	= ORDER_STATUS_PROCESSING;
					$retArr[]	= ORDER_STATUS_SHIPPING_IN_PROGRESS;
					$retArr[]	= ORDER_STATUS_SHIPPED;
					$retArr[]	= ORDER_STATUS_DELIVERED;
					$retArr[]	= ORDER_STATUS_CANCELED;
					break;

				case ORDER_STATUS_PROCESSING:
					$retArr[]	= ORDER_STATUS_PROCESSING;
					$retArr[]	= ORDER_STATUS_SHIPPING_IN_PROGRESS;
					$retArr[]	= ORDER_STATUS_SHIPPED;
					$retArr[]	= ORDER_STATUS_DELIVERED;
					$retArr[]	= ORDER_STATUS_CANCELED;
					break;

				case ORDER_STATUS_SHIPPING_IN_PROGRESS:
					$retArr[]	= ORDER_STATUS_SHIPPING_IN_PROGRESS;
					$retArr[]	= ORDER_STATUS_SHIPPED;
					$retArr[]	= ORDER_STATUS_DELIVERED;
					$retArr[]	= ORDER_STATUS_CANCELED;
					break;

				case ORDER_STATUS_SHIPPED:
					$retArr[]	= ORDER_STATUS_SHIPPED;
					$retArr[]	= ORDER_STATUS_DELIVERED;
					$retArr[]	= ORDER_STATUS_CANCELED;
					break;
			}

			return $retArr;
		}

		public static function HandleBuy(): array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$isGuest = !IS_LOGGED;
			
			$userRetArr = self::GetUserAndSaveIfNew();
			if ($userRetArr["status"] !== STATUS_CODE_SUCCESS) {
				return $userRetArr;
			}
			$userArr = $userRetArr["response"]["user"];
			$userId = $userArr["id"];

			$cartArr = Cart::getFull();

			$data = $cartArr["data"] ?? [];
			$addressId = $cartArr["address_id"] ?? 0;
			$breakdown = $cartArr["breakdown"] ?? [];

			if (count($data) === 0) {
				$retArr["code"] = HTTP_NOTFOUND;
				$retArr["msg"] = "YourCartIsEmpty";
				return self::FixRetArr($retArr);
			}
			
			$addressResponse = Address::LoadAddress($addressId);
			$addressArr = $addressResponse["response"]["address"] ?? [];
			if (is_object($addressArr)) {
				$addressArr = json_decode(json_encode($addressArr), true);
			}

			if ($isGuest) { //Save Address to DB
				unset($addressArr["id"]);
				$addressArr["user_id"] = $userId;
				$addressArr["is_primary"] = 1;
				
				$address = new Address();
				$address->row = $addressArr;
				$address->save();

				$addressArr = $address->row;
				$addressId = $address->row["id"];
			}
			
			if (count($addressArr) === 0) {
				$retArr["code"] = HTTP_NOTFOUND;
				$retArr["msg"] = "CheckoutAddressError";
				return self::FixRetArr($retArr);
			}

			foreach ($data AS $cartGroupId => $cartGroup) {
				$retArr = self::HandleCartGroup($cartGroup["products"], $breakdown, $userArr, $addressArr);
				if ($retArr["status"] !== STATUS_CODE_SUCCESS) {
					return self::FixRetArr($retArr);
				}

				$cartGroup = new CartGroup($cartGroupId);
				$cartGroup->delete();
			}
			
			if ($isGuest) {
				Cart::ClearCookie();
				Cart::ClearGuestCheckoutCookie();
				Address::ClearCookie();
			}

			return self::FixRetArr($retArr);
		}

		private static function HandleCartGroup(array $products, array $breakdown, array $userArr, array $addressArr): array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$userId = Helper::ConvertToInt($userArr["id"]);

			//BEGIN: New Order
			$order	= new self();
			$order->row["user_id"] = $userId;
			$order->row["store_id"] = DEFAULT_STORE_ID;
			$order->row["full_name"] = $addressArr["full_name"] ?? "";
			$order->row["mobile_code"] = $addressArr["mobile_code"] ?? "";
			$order->row["mobile"] = $addressArr["mobile"] ?? "";
			$order->row["landline_code"] = $addressArr["landline_code"] ?? "";
			$order->row["landline"] = $addressArr["landline"] ?? "";
			$order->row["address"] = $addressArr["address"] ?? "";
			$order->row["city"] = $addressArr["city"] ?? "";
			$order->row["state"] = $addressArr["state"] ?? "";
			$order->row["country"] =$addressArr["country"] ??  0;
			$order->row["zipcode"] = $addressArr["zipcode"] ?? "";
			$order->row["invoice_nb"] = self::GenerateInvoiceNb($userId);
			$order->row["payment_method"] = PAYMENT_METHOD_ONLINE;
			$order->row["payment_status"] = PAYMENT_STATUS_UNPAID;
			$order->row["order_status"] = ORDER_STATUS_ERROR_ORDERING;
			$order->row["shipping_provider"] = SHIPPING_PROVIDER_THE_COURIER_GUY;
			$order->row["retail_amount"] = Helper::ConvertToDec($breakdown["retail"]["amount"] ?? 0);
			$order->row["products_amount"] = Helper::ConvertToDec($breakdown["subtotal"]["amount"] ?? 0);
			$order->row["shipping_amount"] = Helper::ConvertToDec($breakdown["shipping"]["amount"] ?? 0);
			$order->row["shipping_amount_main"] = Helper::ConvertToDec($breakdown["shipping"]["amount"] ?? 0);
			$order->row["shipping_code"] = "";
			$order->row["credits_used"] = 0;
			$order->row["commission_rate"] = 0;
			$order->row["protection_expire_on"]	= date(DateHelper::DATETIME_FORMAT_SAVE, time() + (60 * 60 * 24 * 60)); //60 days in the future
			$orderId = $order->insert();
			//END: New Order

			if ($order->error) {
				$retArr["msg"] = _text("OrderPlacedMsgError") . " - " . _text("ErrorCode") . ": " . ERROR_CODE_SAVE_ORDER;
				return self::FixRetArr($retArr);
			}
			else {
				$retArr = self::AddProductsToOrder($products, $orderId);
				if ($retArr["status"] !== STATUS_CODE_SUCCESS) {
					return self::FixRetArr($retArr);
				}

				//Status will be updated and Emails will be sent after online payment
				$order->update([
					"order_status"	=> ORDER_STATUS_AWAITING_ONLINE_PAYMENT
				]);

				$retArr["response"]["order_id"] = $orderId;
			}

			return self::FixRetArr($retArr);
		}


		private static function AddProductsToOrder(array $products, int $orderId): array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			foreach ($products AS $productId => $productOptions) {
				foreach ($productOptions AS $optionIdsStr => $productRow) {
					$productId = Helper::ConvertToInt($productRow["product_id"]);
					$qty = Helper::ConvertToInt($productRow["quantity"]);
					
					$optionsArr = $productRow["options"] ?? [];

					$singleRetailPrice = Helper::ConvertToDec($productRow["final_retail_price"]) / $qty;
					$singleSellPrice = Helper::ConvertToDec($productRow["cart_item_price_after_discount"]);
					
					$product = new Product($productId);
					$stockQty = Helper::ConvertToInt($product->row["stock_quantity"]);
					if ($product->count === 0 || $stockQty <= 0 || $stockQty < $qty) {
						$retArr["msg"] = _text("OrderPlacedMsgError") . " - " . _text("ErrorCode") . ": " . ERROR_CODE_SAVE_ORDER_PRODUCT_QTY;
						return self::FixRetArr($retArr);
					}

					$orderProd = new OrderProduct();
					$orderProd->row["order_id"] = $orderId;
					$orderProd->row["product_id"] = $productId;
					$orderProd->row["option_ids"] = $optionIdsStr;
					$orderProd->row["qty"] = $qty;
					$orderProd->row["single_retail_price"] = $singleRetailPrice;
					$orderProd->row["single_price"] = $singleSellPrice;
					$orderProd->row["product"] = json_encode($productRow);
					$orderProd->insert();

					if ($orderProd->error) {
						$retArr["msg"] = _text("OrderPlacedMsgError") . " - " . _text("ErrorCode") . ": " . ERROR_CODE_SAVE_ORDER_PRODUCT;
						return self::FixRetArr($retArr);
					}
					else {
						$product->row["stock_quantity"] -= $qty;
						$product->update();
						
						$newStockQty = Helper::ConvertToInt($product->row["stock_quantity"]);
						if ($newStockQty === 10 || $newStockQty === 5 || $newStockQty === 0) {
							self::SendProductStockEmail($productId);
						}

						if (count($optionsArr) > 0) {
							foreach ($optionsArr AS $optionRow) {
								$optionId = Helper::ConvertToInt($optionRow["id"]);
								$option = new ProductOption($optionId);
								if ($option->count > 0 && Helper::ConvertToInt($option->row["stock_quantity"]) > 0) {
									$option->row["stock_quantity"] -= $qty;
									$option->update();
									
									$newStockQty = Helper::ConvertToInt($option->row["stock_quantity"]);
									if ($newStockQty === 10 || $newStockQty === 5 || $newStockQty === 0) {
										self::SendProductStockEmail($productId, $optionId);
									}
								}
							}
						}
					}
				}
			}

			$retArr["code"] = HTTP_OK;
			$retArr["msg"] = "";
			
			return self::FixRetArr($retArr);
		}


		private static function GetUserAndSaveIfNew() {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			if (!IS_LOGGED) {
				$guestInfo = Cart::GetGuestCheckoutCookie();
				if (count($guestInfo) === 0) {
					$retArr["redirect"] = getFullUrl(PAGE_CART, LANG);
					return self::FixRetArr($retArr);
				}

				[
					"email" => $email,
					"first_name" => $firstName,
					"last_name" => $lastName,
				] = $guestInfo;

				$user = new User();
				$user->loadByEmail($email);

				if ($user->count > 0) {
					$retArr["msg"] = "EmailAlreadyRegistered";
					$retArr["redirect"] = getFullUrl(PAGE_LOGIN, LANG, [], [
						"return" => Helper::EncryptLoginRedirect(THIS_URL),
						"email" => $email,
						"msg" => base64_encode("EmailAlreadyRegistered")
					]);
					return self::FixRetArr($retArr);
				}
				else { //Create Account (Active - Not Verfied, and Send all emails)
					$pass = rand(100000, 999999);
					$retArr = AuthFormSubmit::Register([
						"terms_agree" => 1,
						"type" => USERTYPE_USER,
						"email" => $email,
						"pass" => $pass,
						"verify_pass" => $pass,
						"default_pass" => $pass,
						"first_name" => $firstName,
						"last_name" => $lastName,
						"mobile_code" => "",
						"mobile" => "",
						"landline_code" => "",
						"landline" => "",
						"address" => "",
						"country_id" => 0,
						"zipcode" => "",
						"register_source" => "checkout",
					]);
					$retArr = self::FixRetArr($retArr);

					if ($retArr["status"] !== STATUS_CODE_SUCCESS) {
						return $retArr;
					}

					$login = AuthFormSubmit::Login([
						"email" => $email,
						"pass" => $pass,
						"skip_verify" => true
					]);
				}
			}
			else {
				$retArr["code"] = HTTP_OK;
				$user = new User(LOGGED_ID);
				$retArr["response"]["user"] = $user->row;
			}

			return self::FixRetArr($retArr);
		}


		private static function FixRetArr(array $retArr) {
			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"] ?? HTTP_INTERNALERROR);
			$retArr["msg"] = Helper::CleanHtmlText($retArr["msg"] ?? "UnknownErrorOccurred");

			return $retArr;
		}

		
		public static function SendProductStockEmail(
			int $productId=0,
			int $optionId=0
		): array {
			$queue = new Queue();
			$queue->name = "SendProductStockEmail";
			$queue->payload = json_encode([
				"product_id" => $productId,
				"option_id" => $optionId,
			]);
			return $queue->SendEmail(false);
		}

	}

?>