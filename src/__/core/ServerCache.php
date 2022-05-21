<?php
	namespace RawadyMario\Classes\Core;

	use RawadyMario\Classes\Database\Views\VProductCategory;
	use RawadyMario\Classes\Helpers\Helper;
	use RawadyMario\Classes\Helpers\MediaHelper;

	class ServerCacheGenerator {
		private const functions = [
			"ProductCategories"
		];

		public static function All(string $version=CACHE_VERSION, bool $force=false) {
			foreach (self::functions AS $function) {
				self::$function($force, $version);
			}
		}

		private static function DeleteCacheIfForce(bool $force=false, string $version=CACHE_VERSION, string $fileName="") {
			if ($force) {
				ServerCache::SetName($fileName);
				ServerCache::SetVersion($version);
				MediaHelper::DeleteFileOrFolder(ServerCache::getCacheFileName());
			}
		}

		public static function ProductCategories(bool $force=false, string $version=CACHE_VERSION) {
			self::DeleteCacheIfForce($force, $version, "v_product_category");

			$v_product_category = ServerCache::GetCache("v_product_category", $version);
			if (Helper::StringNullOrEmpty($v_product_category)) {
				$vCategories = new VProductCategory();
				$vCategories->showActive();
				$vCategories->listAll("1", "", "", "_setByKey");

				ServerCache::SaveCache("v_product_category", $vCategories->data);
			}

			return ServerCache::GetCache("v_product_category", $version);
		}

	}


	class ServerCacheGetter {

		public static function ProductCategories() {
			return ServerCacheGenerator::ProductCategories();
		}

	}
