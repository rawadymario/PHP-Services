<?php
	namespace RawadyMario\Classes\Core\Notification\Resolver;

	use RawadyMario\Classes\Database\Product;
	use RawadyMario\Classes\Database\ProductOption;
	use RawadyMario\Classes\Helpers\Helper;

	class SendProductStockEmailResolver {
		
		public static function GetData(array $payload) {
			$productId = Helper::ConvertToInt($payload["product_id"] ?? 0);
			$optionId = Helper::ConvertToInt($payload["option_id"] ?? 0);
			
			$product = new Product($productId);
			$productName = $product->row["name"];
			$stockQuantity = Helper::ConvertToInt($product->row["stock_quantity"]);
			$productDashboardUrl = getFullUrl(PAGE_PRODUCTS, "", [PAGE_EDIT], ["id"=>$productId], DASHBOARD_ROOT);

			if ($optionId > 0) {
				$option = new ProductOption($optionId);
				if ($option->count > 0) {
					$productName .= " (" . $option->row["title"] . ")";
					$stockQuantity = Helper::ConvertToInt($option->row["stock_quantity"]);
				}
			}

			$payload["product_name"] = $productName;
			$payload["stock_quantity"] = $stockQuantity;
			$payload["button_text"] = "Update Stock";
			$payload["url"] = $productDashboardUrl;
			$payload["subject"] = "Product Stock Notice";

			return $payload;
		}

	}