<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Core\ServerCacheGetter;
	use RawadyMario\Classes\Database\Views\VProduct;
	use RawadyMario\Classes\Database\Views\VProductOption;
	use RawadyMario\Classes\Helpers\Helper;
	use RawadyMario\Classes\Website\WebsiteLinks;

	class Product extends Database {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "product";
			$this->_key		= "id";

			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				$this->clearDeleted();
				parent::load($id);
			}

			$this->decArr	= [
				"retail_price",
				"sell_price",
				"discount_percentage",
				"discount_amount",
				"width",
				"length",
				"height",
				"weight",
			];
			$this->intArr	= [
				"store_id",
				"active",
				"archived",
				"stock_quantity",
			];
		}


		public function loadByMetaUrl(string $url="") : void {
			parent::loadBy($url, "meta_url");
		}


		public function loadByLocation(int $locationId=0) : void {
			$condition = "1";
			if ($locationId === 0) {
				$condition .= " AND `e`.`location` IS NULL";
			}
			else {
				$condition .= " AND FIND_IN_SET($locationId, `e`.`location`)";
			}
			$this->listAll($condition);
		}


		public function loadByType(string $type="") : void {
			$condition = "1";

			switch ($type) {
				case "onsale":
					$condition .= " AND (`e`.`discount_percentage` > 0 OR `e`.`discount_amount` > 0)";
					break;
				
				case "recommended":
				case "popular":
					break;
			}

			$this->listAll($condition);
		}


		public function forWebsite(bool $flag=true) {
			if ($flag) {
				$this->showActive();
				$this->hideArchived();
			}
			else {
				$this->clearActive();
				$this->clearArchived();
			}
		}


		public function isForUser(int $userId=0) : bool {
			return true;
			
			// $store = new Store($this->row["store_id"]);
			// return $store->row["user_id"] == $userId;
		}


		public static function CanSave(array $product=[]) : bool {
			$archived = $product["archived"] ?? 0;
			
			return $archived == 0;
		}

		public static function CanActivate(array $product=[]) : bool {
			$productId	= $product["id"] ?? 0;
			$archived	= $product["archived"] ?? 0;
			
			return $productId > 0 && $archived == 0;
		}

		public static function CanArchive(array $product=[]) : bool {
			$productId	= $product["id"] ?? 0;
			$archived	= $product["archived"] ?? 0;
			
			return $productId > 0 && $archived == 0;
		}

		public static function CanUnarchive(array $product=[]) : bool {
			$productId	= $product["id"] ?? 0;
			$archived	= $product["archived"] ?? 0;
			
			return $productId > 0 && $archived == 1;
		}

		public static function CanUnarchiveAndActivate(array $product=[]) : bool {
			$productId	= $product["id"] ?? 0;
			$active		= $product["active"] ?? 0;
			$archived	= $product["archived"] ?? 0;
			
			return false; //$productId > 0 && $archived == 1 && $active == 0;
		}

		public static function CanDelete(array $product=[]) : bool {
			$productId	= $product["id"] ?? 0;
			$archived	= $product["archived"] ?? 0;
			$active		= $product["active"] ?? 0;
			$ordersNb	= 0;
			
			return $productId > 0 && $archived == 1 && $active == 0 && $ordersNb == 0;
		}

		public static function FixSingleProductElem(array $row, bool $withOptions=false) : array {
			$productId = $row["id"] ?? 0;
			if (!isset($row["id"]) && isset($row["product_id"])) {
				$productId = $row["product_id"];
			}

			if (!isset($row["images"]) || !is_array($row["images"])) {
				$row["images"] = VProduct::GetImages($row["images"] ?? "");
			}

			if (!isset($row["productLink"])) {
				$row["productLink"] = WebsiteLinks::Product($row);
			}

			if (!isset($row["categories"])) {
				$categoriesNice = "";
				$categoriesArr = [];
				$categoryIdsStr = $row["category"] ?? "";

				if ($categoryIdsStr != "") {
					$categoriesCacheArr = ServerCacheGetter::ProductCategories();
					$categoryIdsArr = explode(",", $categoryIdsStr);
	
					foreach ($categoryIdsArr AS $categoryId) {
						$_categoryArr = $categoriesCacheArr[$categoryId] ?? [];

						if (count($_categoryArr) > 0) {
							$categoriesArr[$_categoryArr["id"]] = $_categoryArr;
							$categoriesNice .= ($categoriesNice != "" ? ", " : "") . ($_categoryArr["category"] ?? "");
						}
					}
				}
				$row["categories_nice"] = $categoriesNice;
				$row["categories"] = $categoriesArr;
			}
			
			if (!isset($row["discount_final"])) {
				$discountFinal = 0;
				$discountMinPrice = 0;
				$discountMaxPrice = 0;
				
				$sellPrice = Helper::ConvertToDec($row["sell_price"] ?? 0);
				$minSellPrice = Helper::ConvertToDec($row["min_sell_price"] ?? 0);
				$maxSellPrice = Helper::ConvertToDec($row["max_sell_price"] ?? 0);
				$discountPercentage = Helper::ConvertToDec($row["discount_percentage"] ?? 0);
				$discountAmount = Helper::ConvertToDec($row["discount_amount"] ?? 0);

				if ($discountPercentage > 0) {
					$discountFinal = Helper::ConvertToDec($sellPrice * $discountPercentage * 0.01);
					$discountMinPrice = Helper::ConvertToDec($minSellPrice * $discountPercentage * 0.01);
					$discountMaxPrice = Helper::ConvertToDec($maxSellPrice * $discountPercentage * 0.01);
				}
				else if ($discountAmount > 0) {
					$discountFinal = $discountAmount;
					$discountMinPrice = $discountAmount;
					$discountMaxPrice = $discountAmount;
				}

				$sellPrice -= $discountFinal;
				if ($sellPrice < 0) {
					$sellPrice = 0;
				}

				$minSellPrice -= $discountMinPrice;
				if ($minSellPrice < 0) {
					$minSellPrice = 0;
				}

				$maxSellPrice -= $discountMaxPrice;
				if ($maxSellPrice < 0) {
					$maxSellPrice = 0;
				}

				$discountArr = [];
				$discountArr["sell_price_before_discount"] = Helper::ConvertToDec($row["sell_price"] ?? 0, 2, true, CURRENCY_SIGN);
				$discountArr["min_sell_price_before_discount"] = Helper::ConvertToDec($row["min_sell_price"] ?? 0, 2, true, CURRENCY_SIGN);
				$discountArr["max_sell_price_before_discount"] = Helper::ConvertToDec($row["max_sell_price"] ?? 0, 2, true, CURRENCY_SIGN);
				$discountArr["sell_price_after_discount"] = Helper::ConvertToDec($sellPrice, 2, true, CURRENCY_SIGN);
				$discountArr["min_sell_price_after_discount"] = Helper::ConvertToDec($minSellPrice, 2, true, CURRENCY_SIGN);
				$discountArr["max_sell_price_after_discount"] = Helper::ConvertToDec($maxSellPrice, 2, true, CURRENCY_SIGN);
				
				$discountArr["sell_price"] = Helper::ConvertToDec($sellPrice);
				$discountArr["min_sell_price"] = Helper::ConvertToDec($minSellPrice);
				$discountArr["max_sell_price"] = Helper::ConvertToDec($maxSellPrice);
				$discountArr["discount_percentage"] = Helper::ConvertToDec($discountPercentage);
				$discountArr["discount_final"] = Helper::ConvertToDec($discountFinal);
				
				$row = array_merge($row, $discountArr);
			}

			$haveDiscount = Helper::ConvertToDec($row["discount_final"] ?? 0) > 0;

			if (!isset($row["sell_price_nice"])) {
				$sellPriceNiceBeforeDiscount = "";
				if ($haveDiscount) {
					$min = $row["min_sell_price_before_discount"];
					$max = $row["max_sell_price_before_discount"];
				
					$cls = "jsDiscountPrice";
					if ($min === $max) {
						$title = $min;
						$cls .= " one-price";
					}
					else {
						$title = $min . ' - ' . $max;
						$cls .= " two-prices";
					}
					$sellPriceNiceBeforeDiscount = '<del class="' . $cls . '" title="' . $title . '">' . $title . '</del>';
				}

				$min = $row["min_sell_price_after_discount"];
				$max = $row["max_sell_price_after_discount"];

				$cls = "jsFinalPrice";
				if ($min === $max) {
					$title = $min;
					$cls .= " one-price";
				}
				else {
					$title = $min . ' - ' . $max;
					$cls .= " two-prices";
				}
				$data = [
					"class" => $cls,
					"title" => $title,
					"data-defaultpricepreview" => $title,
					"data-defaultprice" => $row["min_sell_price"] ?? Helper::ConvertToDec(str_replace(CURRENCY_SIGN, "", $min)),
				];
				$sellPriceNiceAfterDiscount = '<ins ' . Helper::GererateKeyValueStringFromArray($data) . '>' . $title . '</ins>';

				$niceArr = [];
				$niceArr["sell_price_nice_before_discount"] = $sellPriceNiceBeforeDiscount;
				$niceArr["sell_price_nice_after_discount"] = $sellPriceNiceAfterDiscount;
				$niceArr["sell_price_nice"] = $sellPriceNiceAfterDiscount;
				
				$row = array_merge($row, $niceArr);
			}

			if (!isset($row["discount_nice"])) {
				$discountNice = "";
				
				$discountPercentage = $row["discount_percentage"] ?? 0;
				$discountAmount = $row["discount_amount"] ?? 0;

				if ($discountPercentage > 0) {
					$discountNice = Helper::ConvertToDec($discountPercentage, 2, true) . "%";
				}
				else if ($discountAmount > 0) {
					$discountNice = Helper::ConvertToDec($discountAmount, 2, true, CURRENCY_SIGN);
				}

				$row["discount_nice"] = $discountNice;
			}

			if ($withOptions) {
				if (!isset($row["options"])) {
					$options = new VProductOption();
					$options->forWebsite(true);
					$options->loadByProduct($productId, [
						"function" => "_groupByCategoryId"
					]);
	
					$row["options"] = $options->list;
				}
			}

			$row["cart_item_price_before_discount"] = Helper::ConvertToDec($row["sell_price"]);
			$row["cart_item_price_after_discount"] = Helper::ConvertToDec($row["sell_price"]);
			$row["final_retail_price"] = Helper::ConvertToDec($row["retail_price"]);
			if (isset($row["options"]) && count($row["options"]) > 0) {
				$discountPercentage = Helper::ConvertToDec($row["discount_percentage"]);

				foreach ($row["options"] AS $_opt) {
					$_optRetail = Helper::ConvertToDec($_opt["additional_retail"] ?? 0);
					$_optPrice = Helper::ConvertToDec($_opt["additional_sell"] ?? 0);
					
					if ($_optPrice > 0) {
						$row["cart_item_price_before_discount"] += $_optPrice;
						if ($discountPercentage > 0) {
							$_optPrice -= $_optPrice * $discountPercentage * 0.01;
						}
						$row["cart_item_price_after_discount"] += $_optPrice;
					}
					if ($_optRetail > 0) {
						$row["final_retail_price"] += $_optRetail;
					}
				}
			}

			$row["cart_item_price_nice"] = '';
			if ($haveDiscount) {
				$row["cart_item_price_nice"] .= '<del>' . Helper::ConvertToDec($row["cart_item_price_before_discount"], 2, true, CURRENCY_SIGN) . '</del>';
			}
			$row["cart_item_price_nice"] .= '<ins>' . Helper::ConvertToDec($row["cart_item_price_after_discount"], 2, true, CURRENCY_SIGN) . '</ins>';
			
			$quantity = Helper::ConvertToInt($row["quantity"] ?? 0);
			if ($quantity > 0) {
				$row["cart_total_price_before_discount"] = $row["cart_item_price_before_discount"] * $quantity;
				$row["cart_total_price_after_discount"] = $row["cart_item_price_after_discount"] * $quantity;
				$row["final_retail_price"] = $row["final_retail_price"] * $quantity;
				
				$row["cart_total_price_nice"] = '';
				if ($haveDiscount) {
					$row["cart_total_price_nice"] .= '<del>' . Helper::ConvertToDec($row["cart_total_price_before_discount"], 2, true, CURRENCY_SIGN) . '</del>';
				}
				$row["cart_total_price_nice"] .= '<ins>' . Helper::ConvertToDec($row["cart_total_price_after_discount"], 2, true, CURRENCY_SIGN) . '</ins>';
			}

			return $row;
		}
		
	}