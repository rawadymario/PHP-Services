<?php
	namespace RawadyMario\Helpers;


	class TranslateHelper {
		private static $VALS = [];


		/**
		 * Add all the default translations
		 */
		public static function AddDefaults(): void {
			//TODO: Behavior to read from default_translations and add them to self::$VALS
		}

		
		/**
		 * Add a translation value to the $VALS variable 
		 */
		public static function AddValue(string $value, string $valueKey, string $lang): void {
			if (!isset(self::$VALS[$valueKey])) {
				self::$VALS[$valueKey] = [];
			}
			if (!isset(self::$VALS[$valueKey][$lang])) {
				self::$VALS[$valueKey][$lang] = $value;
			}
		}


		/**
		 * Return the Translation of the Given Key in the given Language
		 */
		public static function Translate(string $key, ?string $lang="", bool $returnEmpty=false, array $replace=[]): string {
			if (Helper::StringNullOrEmpty($lang)) {
				$lang = LangHelper::$ACTIVE;
			}
	
			$str = $key;
			if (isset(self::$VALS[$key]) && isset(self::$VALS[$key][$lang])) {
				$str = self::$VALS[$key][$lang];
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