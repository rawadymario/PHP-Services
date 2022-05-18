<?php
	namespace RawadyMario\Classes\Core;

	use RawadyMario\Classes\Database\Views\VProductCategory;
	use RawadyMario\Classes\Helpers\Helper;
	use RawadyMario\Classes\Helpers\MediaHelper;

	class ServerCache {
		const RootFolder = ROOT_DIR;
		const CacheFolderName = "cache";

		private static $name;
		private static $version;
		
		private static $cacheFolderName;
		private static $versionFolderName;
		private static $cacheFileName;

		public static function SaveCache(string $name, $value, string $version=CACHE_VERSION) {
			self::SetName($name);
			self::SetVersion($version);
			
			MediaHelper::CreateFileOrFolder(self::$cacheFolderName);
			MediaHelper::CreateFileOrFolder(self::$versionFolderName);
			
			self::SaveFile(self::$cacheFileName, $value);
		}

		public static function GetCache(string $name, string $version=CACHE_VERSION, bool $asArray=true) {
			$ret = "";
			self::SetName($name);
			self::SetVersion($version);
			
			if (file_exists(self::$cacheFileName)) {
				$ret = file_get_contents(self::$cacheFileName);
				
				if ($asArray) {
					$ret = json_decode($ret, true);
				}
			}

			return $ret;
		}

		private static function SaveFile(string $path, $content) {
			if (in_array(gettype($content), ["array", "object"])) {
				$content = json_encode($content);
			}
			
			$cacheFile = fopen($path, "w") or die("Unable to open cache file!");
			fwrite($cacheFile, $content);
			fclose($cacheFile);
		}


		public static function SetName(string $name) {
			self::$name = $name;
			self::SetFoldersAndFiles();
		}

		public static function SetVersion(string $version) {
			self::$version = $version;
			self::SetFoldersAndFiles();
		}

		private static function SetFoldersAndFiles() {
			if (self::$version) {
				self::$cacheFolderName = self::RootFolder . self::CacheFolderName;
				self::$versionFolderName = self::RootFolder . self::CacheFolderName . "/" . self::$version;

				if (self::$name) {
					self::$cacheFileName = self::RootFolder . self::CacheFolderName . "/" . self::$version . "/" . self::$name . ".txt";
				}
			}
		}

		public static function getCacheFileName() {
			return self::$cacheFileName;
		}

	}


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