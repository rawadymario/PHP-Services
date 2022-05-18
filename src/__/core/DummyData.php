<?php
	namespace RawadyMario\Classes\Core;

	use RawadyMario\Classes\Database\Product;
	use RawadyMario\Classes\Database\ProductCategory;
	use RawadyMario\Classes\Database\ProductImage;
	use RawadyMario\Classes\Database\ProductLocation;
	use RawadyMario\Classes\Database\ProductOption;
	use RawadyMario\Classes\Helpers\Helper;

	class DummyData {
		private static $echo = [];

		public static function AddAll() : string {
			self::AddProductCategories();
			self::AddProducts();

			if (count(self::$echo) == 0) {
				self::$echo[] = "Nothing to add!";
			}

			return implode("<br />", self::$echo);
		}

		private static function AddProductCategories() : void {
			$max = 15;
			$categories = new ProductCategory();
			$categories->showActive();
			$categories->listAll();
			if ($categories->count < $max) {
				$count = 0;
				$toAdd = $max - $categories->count;
				
				while ($count < $toAdd) {
					$imgPath = sprintf("assets/default_images/img-%03d.jpg", rand(1, 15));

					$category = new ProductCategory();
					$category->insert([
						"parent_id" => 0,
						"category" => "Category " . sprintf("%03d", $count + 1),
						"active" => 1,
						"image" => $imgPath,
						"meta_url" => Helper::SafeUrl("Category " . sprintf("%03d", $count + 1)),
						"meta_title" => "Category " . sprintf("%03d", $count + 1),
						"meta_author" => CLIENT_NAME,
						"meta_keywords" => "Keyword 001, Keyword 002, Keyword 003, Keyword 004, Keyword 005",
						"meta_desc" => "Description " . sprintf("%03d", $count + 1) . " goes here!",
					]);

					$count++;
				}

				self::$echo[] = "$count Product Category Added!";
			}
		}

		private static function AddProducts() : void {
			$max = 100;
			$products = new Product();
			$products->showActive();
			$products->listAll();
			if ($products->count < $max) {
				$categoryIds = [];
				$categories = new ProductCategory();
				$categories->showActive();
				$categories->listAll();
				foreach ($categories->data AS $category) {
					$categoryIds[] = $category["id"];
				}
				
				$locationIds = [];
				$locations = new ProductLocation();
				$locations->showActive();
				$locations->listAll();
				foreach ($locations->data AS $location) {
					$locationIds[] = $location["id"];
				}
				
				$count = 0;
				$toAdd = $max - $products->count;
				
				while ($count < $toAdd) {
					$nbOfCategories = rand(1, 5);
					$productCategories = [];
					while ($nbOfCategories > 0) {
						$found = false;

						while (!$found) {
							$index = rand(0, count($categoryIds) - 1);
							$categoryId = $categoryIds[$index];

							if (!in_array($categoryId, $productCategories)) {
								$found = true;
								$productCategories[] = $categoryIds[$index];
							}
						}

						$nbOfCategories--;
					}
					$productCategoriesStr = implode(",", $productCategories);
					
					$nbOfLocations = rand(0, count($locationIds));
					$locationsArr = [];
					while ($nbOfLocations > 0) {
						$found = false;

						while (!$found) {
							$index = rand(0, count($locationIds) - 1);
							$locationId = $locationIds[$index];

							if (!in_array($locationId, $locationsArr)) {
								$found = true;
								$locationsArr[] = $locationIds[$index];
							}
						}

						$nbOfLocations--;
					}
					$locationsStr = implode(",", $locationsArr);

					$retailPrice = rand(5, 150);
					$sellPrice = $retailPrice * 1.35;

					$discountPercentage = 0;
					$discountAmount = 0;
					if ($count % 3 == 1) {
						$discountPercentage = rand(0, 30);
					}
					if ($count % 2 == 1) {
						$discountAmount = rand(0, ($sellPrice * 0.85));
					}

					$values = [
						"store_id" => DEFAULT_STORE_ID,
						"active" => 1,
						"archived" => 0,
						"category" => $productCategoriesStr,
						"location" => $locationsStr,
						"name" => "Product " . sprintf("%03d", $count + 1),
						"summary" => "Summary " . sprintf("%03d", $count + 1),
						"description" => "<p>Description " . sprintf("%03d", $count + 1) . " goes here!</p>",
						"retail_price" => $retailPrice,
						"sell_price" => $sellPrice,
						"discount_percentage" => $discountPercentage,
						"discount_amount" => $discountAmount,
						"stock_quantity" => rand(50, 500),
						"width" => rand(1, 10),
						"length" => rand(1, 10),
						"height" => rand(1, 10),
						"weight" => rand(1, 10),
						"meta_url" => Helper::SafeUrl("Product " . sprintf("%03d", $count + 1)),
						"meta_title" => "Product " . sprintf("%03d", $count + 1),
						"meta_author" => CLIENT_NAME,
						"meta_keywords" => "Keyword 001, Keyword 002, Keyword 003, Keyword 004, Keyword 005",
						"meta_desc" => "Description " . sprintf("%03d", $count + 1) . " goes here!",
					];

					$product = new Product();
					$productId = $product->insert($values);

					self::AddProductImages($productId);
					self::AddProductOptions($productId);
					
					$count++;
				}

				self::$echo[] = "$count Product Added!";
			}
		}

		private static function AddProductImages(int $productId) : void {
			$imagesArr = [];
			while (count($imagesArr) < 5) {
				$imgPath = sprintf("assets/default_images/img-%03d.jpg", rand(1, 15));
				if (!in_array($imgPath, $imagesArr)) {
					$imagesArr[] = $imgPath;
				}
			}
			$o = 0;
			foreach ($imagesArr AS $imagePath) {
				$values = [
					"product_id" => $productId,
					"order" => $o,
					"image_path" => $imagePath,
				];
				$productImage = new ProductImage();
				$productImage->insert($values);
				$o++;
			}
		}

		private static function AddProductOptions(int $productId) : void {
			$optionsArr = [
				[
					"category_id" => 1,
					"product_id" => $productId,
					"order" => 0,
					"title" => "Blue",
					"color" => "#0094ff",
					"additional_retail" => 0,
					"additional_sell" => 0,
					"stock_quantity" => 50,
				],
				[
					"category_id" => 1,
					"product_id" => $productId,
					"order" => 1,
					"title" => "Green",
					"color" => "#00ff29",
					"additional_retail" => 0,
					"additional_sell" => 0,
					"stock_quantity" => 50,
				],
				[
					"category_id" => 1,
					"product_id" => $productId,
					"order" => 2,
					"title" => "Red",
					"color" => "#ff2525",
					"additional_retail" => 0,
					"additional_sell" => 0,
					"stock_quantity" => 50,
				],

				[
					"category_id" => 2,
					"product_id" => $productId,
					"order" => 0,
					"title" => "S",
					"additional_retail" => 0,
					"additional_sell" => 0,
					"stock_quantity" => 50,
				],
				[
					"category_id" => 2,
					"product_id" => $productId,
					"order" => 1,
					"title" => "M",
					"additional_retail" => 0,
					"additional_sell" => 0,
					"stock_quantity" => 50,
				],
				[
					"category_id" => 2,
					"product_id" => $productId,
					"order" => 2,
					"title" => "L",
					"additional_retail" => 0,
					"additional_sell" => 0,
					"stock_quantity" => 50,
				],

				[
					"category_id" => 3,
					"product_id" => $productId,
					"order" => 0,
					"title" => "B1",
					"additional_retail" => 0,
					"additional_sell" => 0,
					"stock_quantity" => 50,
				],
				[
					"category_id" => 3,
					"product_id" => $productId,
					"order" => 1,
					"title" => "B2",
					"additional_retail" => 0,
					"additional_sell" => 0,
					"stock_quantity" => 50,
				],
				[
					"category_id" => 3,
					"product_id" => $productId,
					"order" => 2,
					"title" => "B3",
					"additional_retail" => 0,
					"additional_sell" => 0,
					"stock_quantity" => 50,
				],
			];

			foreach ($optionsArr AS $optionRow) {
				$productOption = new ProductOption();
				$productOption->insert($optionRow);
			}
		}

	}