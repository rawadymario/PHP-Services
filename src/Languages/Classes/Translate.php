<?php
	namespace RawadyMario\Languages\Classes;

	use RawadyMario\Exceptions\NotEmptyParamException;
	use RawadyMario\Helpers\Helper;

	class Translate {
		private static $VALS_WITHOUT_TYPE = [];
		private static $VALS = [];


		public static function GetTranlationsArray(): array {
			return self::$VALS;
		}


		public static function clear(): void {
			self::$VALS_WITHOUT_TYPE = [];
			self::$VALS = [];
		}


		/**
		 * Add all the default translations (Read from Mappings and add them to self::$VALS)
		 */
		public static function AddDefaults(): void {
			$dir = __DIR__ . "\..\Mappings";
			$filesArr = Helper::GetAllFiles($dir);

			foreach ($filesArr AS $filePath) {
				self::AddFileContentToVals($filePath);
			}
		}


		/**
		 * Add all the custom translations (Read from the provided directory and add them to self::$VALS)
		 */
		public static function AddCustomDir(
			string $customDir
		): void {
			if (!Helper::StringNullOrEmpty($customDir) && is_dir($customDir)) {
				$filesArr = Helper::GetAllFiles($customDir);

				foreach ($filesArr AS $filePath) {
					self::AddFileContentToVals($filePath);
				}
			}
		}


		/**
		 * Return the Translation of the Given Key in the given Language
		 */
		public static function Translate(
			?string $key,
			?string $lang=null,
			bool $returnEmpty=false,
			array $replace=[],
			bool $withType=true
		): string {
			if (Helper::StringNullOrEmpty($key)) {
				throw new NotEmptyParamException("key");
			}
			if (Helper::StringNullOrEmpty($lang)) {
				$lang = Language::$ACTIVE;
			}

			$vals = !$withType ? self::$VALS_WITHOUT_TYPE : self::$VALS;
			if (Helper::ArrayNullOrEmpty($vals)) {
				self::AddDefaults();
				$vals = !$withType ? self::$VALS_WITHOUT_TYPE : self::$VALS;
			}

			$str = $key;
			if (isset($vals[$key . "." . $lang])) {
				$str = $vals[$key . "." . $lang];
			}
			else if ($returnEmpty) {
				$str = "";
			}

			if (!Helper::StringNullOrEmpty($str) && count($replace) > 0) {
				$str = str_replace(
					array_keys($replace),
					array_values($replace),
					$str
				);
			}

			return $str;
		}


		/**
		 * Same as Translate, but having the parameter "withType = false"
		 */
		public static function TranslateSimple(
			?string $key,
			?string $lang=null,
			bool $returnEmpty=false,
			array $replace=[]
		): string {
			return self::Translate($key, $lang, $returnEmpty, $replace, false);
		}


		/**
		 * Return the Translation of the Given String in the given Language
		 */
		public static function TranslateString(
			?string $string,
			?string $lang=null,
			array $replace=[],
			bool $withType=true
		): string {
			if (Helper::StringNullOrEmpty($string)) {
				throw new NotEmptyParamException("string");
			}
			if (Helper::StringNullOrEmpty($lang)) {
				$lang = Language::$ACTIVE;
			}

			$vals = !$withType ? self::$VALS_WITHOUT_TYPE : self::$VALS;
			if (Helper::ArrayNullOrEmpty($vals)) {
				self::AddDefaults();
				$vals = !$withType ? self::$VALS_WITHOUT_TYPE : self::$VALS;
			}

			foreach ($vals AS $k => $v) {
				$_langKey = "." . $lang;
				if (Helper::StringEndsWith($k, "." . $lang)) {
					$k = str_replace($_langKey, "", $k);
					$string = str_replace($k, $v, $string);
				}
			}

			if (!Helper::StringNullOrEmpty($string) && count($replace) > 0) {
				foreach ($replace AS $k => $v) {
					$string = str_replace($k, $v, $string);
				}
			}

			return $string;
		}


		/**
		 * Same as TranslateString, but having the parameter "withType = false"
		 */
		public static function TranslateStringSimple(
			?string $string,
			?string $lang=null,
			array $replace=[]
		): string {
			return self::TranslateString($string, $lang, $replace, false);
		}


		private static function AddFileContentToVals(
			string $filePath
		): void {
			$fileArr = Helper::GetJsonContentFromFileAsArray($filePath);

			$pathArr = explode("/", $filePath);
			$lastPath = $pathArr[sizeof($pathArr) - 1];
			$type = strtolower(str_replace(".json", "", $lastPath));

			self::$VALS = array_merge(self::$VALS, Helper::ConvertMultidimentionArrayToSingleDimention($fileArr));
			self::$VALS_WITHOUT_TYPE = array_merge(self::$VALS_WITHOUT_TYPE, Helper::ConvertMultidimentionArrayToSingleDimention($fileArr[$type]));
		}

	}
