<?php
	namespace DigitalSplash\ServerCache;

	class ServerCache {
		private static string $RootFolder = '';
		private static string $CacheFolderName = "cache";

		private static $name;
		private static $version;

		private static $cacheFolderName;
		private static $versionFolderName;
		private static $cacheFileName;

		public static function setRootFolder(string $rootFolder): void {
			self::$RootFolder = constant($rootFolder);
		}

		public static function setCacheFolderName(string $cacheFolderName): void {
			self::$CacheFolderName = $cacheFolderName;
		}

		public static function SaveCache(
			$value,
			string $name,
			?string $version=null
		): void {
			self::SetName($name);
			if (!empty($version)) {
				self::SetVersion($version);
			}

			self::CreateFolder(self::$cacheFolderName);
			self::CreateFolder(self::$versionFolderName);

			self::SaveFile(self::$cacheFileName, $value);
		}

		public static function GetCache(
			bool $asArray,
			string $name,
			?string $version=null
		): array {
			$ret = [];

			self::SetName($name);
			if (!empty($version)) {
				self::SetVersion($version);
			}

			if (file_exists(self::$cacheFileName)) {
				$ret = file_get_contents(self::$cacheFileName);

				if ($asArray) {
					$ret = json_decode($ret, true);
				}
			}

			return $ret;
		}

		private static function SetName(string $name=''): void {
			if ($name != "") {
				self::$name = $name;
				self::SetFoldersAndFiles();
			}
		}

		public static function SetVersion(string $version=''): void {
			if ($version != "") {
				self::$version = $version;
				self::SetFoldersAndFiles();
			}
		}

		private static function SetFoldersAndFiles(): void {
			if (self::$version) {
				self::$cacheFolderName = self::$RootFolder . self::$CacheFolderName;
				self::$versionFolderName = self::$RootFolder . self::$CacheFolderName . "/" . self::$version;

				if (self::$name) {
					self::$cacheFileName = self::$RootFolder . self::$CacheFolderName . "/" . self::$version . "/" . self::$name . ".txt";
				}
			}
		}

		private static function CreateFolder(string $dir): void {
			if (!file_exists($dir) && !is_dir($dir)) {
				mkdir($dir, 0777);
			}
		}

		private static function SaveFile(
			string $path,
			$content
		): void {
			if (in_array(gettype($content), ["array", "object"])) {
				$content = json_encode($content);
			}

			$cacheFile = fopen($path, "w") or die("Unable to open cache file!");
			fwrite($cacheFile, $content);
			fclose($cacheFile);
		}

	}

?>
