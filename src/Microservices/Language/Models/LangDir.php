<?php
	namespace RawadyMario\Language\Models;


	class LangDir {
		public const LTR = "ltr";
		public const RTL = "rtl";

		private const MAPPING = [
			self::LTR => [
				Lang::EN,
				Lang::FR,
			],
			self::RTL => [
				Lang::AR
			],
		];

		public static function GetDirByLanguage(
			string $lang
		): string {
			$dir = "";

			$filtered_array = array_map(function($langsByDir) use ($lang) {
				return in_array($lang, $langsByDir) ? $lang : "";
			}, self::MAPPING);

			if (in_array($lang, $filtered_array)) {
				$dir = array_search($lang, $filtered_array);
			}

			return $dir;
		}
	}
