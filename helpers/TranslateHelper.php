<?php
	namespace RawadyMario\Helpers;

	class TranslateHelper {
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

				self::$VALS = array_merge(self::$VALS, Helper::ConvertMultidimentionArrayToSingleDimention($fileArr));
			}
		}


		/**
		 * Return the Translation of the Given Key in the given Language
		 */
		public static function Translate(
			string $key,
			?string $lang="",
			bool $returnEmpty=false,
			array $replace=[]
		): string {
			if (Helper::ArrayNullOrEmpty(self::$VALS)) {
				self::AddDefaults();
			}
			if (Helper::StringNullOrEmpty($lang)) {
				$lang = LangHelper::$ACTIVE;
			}
			
			$str = $key;
			if (isset(self::$VALS[$key . "." . $lang])) {
				$str = self::$VALS[$key . "." . $lang];
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
		
	}
