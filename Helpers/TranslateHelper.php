<?php
	namespace RawadyMario\Helpers;

	class TranslateHelper {
		private static $VALS_WITHOUT_TYPE = [];
		private static $VALS = [];


		public static function GetTranlationsArray(): array {
			return self::$VALS;
		}


		/**
		 * Add all the default translations (Read from DefaultTranslations and add them to self::$VALS)
		 */
		public static function AddDefaults(): void {
			$dir = __DIR__ . "\..\DefaultTranslations";
			$filesArr = Helper::GetAllFiles($dir);

			foreach ($filesArr AS $filePath) {
				$fileArr = Helper::GetJsonContentFromFileAsArray($filePath);

				$pathArr = explode("/", $filePath);
				$lastPath = $pathArr[sizeof($pathArr) - 1];
				$type = strtolower(str_replace(".json", "", $lastPath));
				
				self::$VALS = array_merge(self::$VALS, Helper::ConvertMultidimentionArrayToSingleDimention($fileArr));
				self::$VALS_WITHOUT_TYPE = array_merge(self::$VALS_WITHOUT_TYPE, Helper::ConvertMultidimentionArrayToSingleDimention($fileArr[$type]));
			}
		}


		/**
		 * Return the Translation of the Given Key in the given Language
		 */
		public static function Translate(
			?string $key,
			?string $lang="",
			bool $returnEmpty=false,
			array $replace=[],
			bool $withType=true
		): string {
			if (Helper::StringNullOrEmpty($key)) {
				return "";
			}
			
			$vals = !$withType ? self::$VALS_WITHOUT_TYPE : self::$VALS;

			if (Helper::ArrayNullOrEmpty($vals)) {
				self::AddDefaults();
			}
			if (Helper::StringNullOrEmpty($lang)) {
				$lang = LangHelper::$ACTIVE;
			}
			
			$str = $key;
			if (isset($vals[$key . "." . $lang])) {
				$str = $vals[$key . "." . $lang];
			}
			else if ($returnEmpty) {
				$str = "";
			}
	
			if (!Helper::StringNullOrEmpty($str) && count($replace) > 0) {
				foreach ($replace AS $k => $v) {
					$str = str_replace($k, $v, $str);
				}
			}
	
			return $str;
		}


		/**
		 * Return the Translation of the Given String in the given Language
		 */
		public static function TranslateString(
			?string $string,
			?string $lang="",
			array $replace=[],
			bool $withType=true
		): string {
			if (Helper::StringNullOrEmpty($string)) {
				return "";
			}
			
			$vals = !$withType ? self::$VALS_WITHOUT_TYPE : self::$VALS;

			if (Helper::ArrayNullOrEmpty($vals)) {
				self::AddDefaults();
			}
			if (Helper::StringNullOrEmpty($lang)) {
				$lang = LangHelper::$ACTIVE;
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
					$str = str_replace($k, $v, $string);
				}
			}
	
			return $string;
		}
		
	}
