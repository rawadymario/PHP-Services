<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Constants\Lang;

	class LangHelper {
		public static $DEFAULT = Lang::EN;
		public static $ACTIVE = Lang::EN;


		/**
		 * Set the $DEFAULT variable
		 */
		public static function SetVariableDefault(
			string $var
		): void {
			self::$DEFAULT = $var;
		}


		/**
		 * Set the $ACTIVE variable
		 */
		public static function SetVariableActive(
			string $var
		): void {
			self::$ACTIVE = $var;
		}


		/**
		 * Convert Special Lowercase Characters to Uppercase
		 */
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


		/**
		 * Get field key
		 */
		public static function GetFieldKey(
			string $field,
			string $lang=""
		): string {
			if (Helper::StringNullOrEmpty($lang)) {
				$lang = self::$ACTIVE;
			}

			return $lang == Lang::EN ? $field : $field . "_" . $lang;
		}

	}
