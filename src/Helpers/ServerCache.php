<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Exceptions\FileNotFoundException;
	use RawadyMario\Exceptions\NotEmptyParamException;

	class ServerCache {
		protected static ?string $version = null;

		protected static ?string $cacheFolder = null;
		protected static ?string $versionFolder = null;

		public static function Get(string $name, bool $asArray=false) {
			$cacheFileName = self::GetCacheFileName($name);

			if (!file_exists($cacheFileName)) {
				throw new FileNotFoundException("cacheFileName");
			}

			$ret = Helper::GetContentFromFile($cacheFileName);
			if ($asArray) {
				$ret = json_decode($ret, true);
			}
			return $ret;
		}

		public static function Set(string $name, $value): void {
			self::SetVersionFolderAndCacheFile();

			if (in_array(gettype($value), ["array", "object"])) {
				$value = json_encode($value);
			}
			
			$cacheFile = fopen(self::GetCacheFileName($name), "w") or die("Unable to open cache file!");
			fwrite($cacheFile, $value);
			fclose($cacheFile);
		}

		public static function GetVersion(): string {
			return self::$version;
		}

		public static function SetVersion(string $version): void {
			self::$version = $version;
		}

		public static function GetCacheFolder(): string {
			return self::$cacheFolder;
		}

		public static function SetCacheFolder(string $cacheFolder): void {
			self::$cacheFolder = $cacheFolder;
		}

		public static function GetVersionFolder(): string {
			return self::$versionFolder;
		}

		protected static function SetVersionFolder(string $versionFolder): void {
			self::$versionFolder = $versionFolder;
		}

		public static function GetCacheFileName(string $name): string {
			if (Helper::StringNullOrEmpty(self::$versionFolder)) {
				throw new NotEmptyParamException("ServerCache::\$versionFolder");
			}
			return self::$versionFolder . "/" . $name . ".txt";
		}

		protected static function SetVersionFolderAndCacheFile(): void {
			if (!Helper::StringNullOrEmpty(self::$version) && !Helper::StringNullOrEmpty(self::$cacheFolder)) {
				self::SetVersionFolder(self::$cacheFolder . "/" . self::$version);
				Helper::CreateFolderRecursive(self::$versionFolder);
			}
		}

	}
