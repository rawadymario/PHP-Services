<?php
	namespace RawadyMario\Helpers;

	class AntiHack {
		protected static array $unsecureValues = [
			"select",
			"from",
			"join",
			"inner",
			"outer",
			"where",
			"having",
			"group by",
			"order by",
			"limit",
			"union"
		];

		public static function AddUnsecureValue(string $unsecureValue): void {
			if (!in_array($unsecureValue, self::$unsecureValues)) {
				self::$unsecureValues[] = $unsecureValue;
			}
		}

		public static function RemoveUnsecureValue(string $unsecureValue): void {
			if (($key = array_search($unsecureValue, self::$unsecureValues)) !== false) {
				unset(self::$unsecureValues[$key]);
			}
		}

		public static function ClearUnsecureValues(): void {
			self::$unsecureValues = [];
		}

		public static function GetUnsecureValues(): array {
			return self::$unsecureValues;
		}

		/**
		 * Main Function to Check the Given Array for any Hacking Possibility
		 */
		public static function Check(
			array $arrayToFix=[],
			array $customKeys=[],
			bool $checkUnsecureValues=true
		): array {
			foreach ($arrayToFix AS $k => $str) {
				if (is_array($arrayToFix[$k])) {
					$arrayToFix[$k] = self::Check($arrayToFix[$k], $customKeys, $checkUnsecureValues);
				}
				else {
					$arrayToFix[$k] = self::ValidateAndFixString($str, $customKeys, $checkUnsecureValues);
				}
			}

			return $arrayToFix;
		}

		protected static function ValidateAndFixString(
			string $str="",
			array $customKeys=[],
			bool $checkUnsecureValues=false
		): string {
			$str = self::PreventSqlInjection($str);
			$str = self::FixCustomParams($str, $customKeys);

			if ($checkUnsecureValues) {
				$str = self::CheckForUnsecureValues($str, 2);
			}

			return $str;
		}

		/**
		 * Prevent SQL Injection and XSS
		 */
		protected static function PreventSqlInjection(
			string $str=""
		): string {
			$str = trim($str);
			$str = stripslashes($str);

			return $str;
		}

		/**
		 * Check if the given String contain any insecure Value
		 */
		private static function CheckForUnsecureValues(
			string $str="",
			int $minCount=0
		): string {
			$unsecureValuesFound = [];

			$str = urldecode($str);

			foreach (self::$unsecureValues AS $unsecureString) {
				if (strpos(strtolower($str), strtolower($unsecureString)) !== false) {
					$unsecureValuesFound[] = $unsecureString;
				}
			}

			if ($minCount > 0 && count($unsecureValuesFound) >= $minCount) {
				foreach ($unsecureValuesFound AS $unsecureString) {
					$str = str_replace(strtolower($unsecureString), "_" . strtolower($unsecureString) . "_", strtolower($str));
				}
			}

			return $str;
		}

		/**
		 * Fix Values by Custom Params
		 */
		protected static function FixCustomParams(
			string $str="",
			array $customKeys=[]
		): string {
			foreach ($customKeys AS $customKey) {
				switch ($customKey) {
					case "query":
						if (strlen($str) > 50) {
							$str = Helper::TruncateStr($str, 50, "");
						}
					break;
				}
			}

			return $str;
		}

	}
