<?php
	namespace RawadyMario\Language\Helpers;

	use RawadyMario\Helpers\Helper;
	use RawadyMario\Language\Models\Lang;

	class Language {
		private static string $default = Lang::EN;
		private static string $active = Lang::EN;
		private static array $allowed = [];


		public static function Uppercase(
			string $val
		): string {
			$val = strtoupper($val);

			$val = str_replace([
				"à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï",
			], [
				"À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï",
			], $val);

			return $val;
		}

		public static function GetFieldKey(
			string $field,
			?string $lang=null
		): string {
			if (Helper::StringNullOrEmpty($lang)) {
				$lang = self::GetActive();
			}

			return $lang == Lang::EN ? $field : $field . "_" . $lang;
		}

		public static function GetDefault(): string {
			return self::$default;
		}

		public static function SetDefault(
			string $var
		): void {
			self::$default = $var;
		}

		public static function GetActive(): string {
			return self::$active;
		}

		public static function SetActive(
			string $var
		): void {
			self::$active = $var;
		}

		public static function AddToAllowed(string $value): void {
			if (!in_array($value, self::$allowed)) {
				array_push(self::$allowed, $value);
			}
		}

		public static function RemoveFromAllowed(string $value): void {
			if (in_array($value, self::$allowed)) {
				$indexToRemove = array_search($value, self::$allowed);
				unset(self::$allowed[$indexToRemove]);
			}
		}

		public static function GetAllowed(): array {
			return self::$allowed;
		}

		public static function ClearAllowed(): void {
			self::$allowed = [];
		}

	}
