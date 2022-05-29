<?php
	class AntiHack {
		const UnsecureVals = [
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

		/**
		 * Main Function to Check the Given Array for any Hacking Possibility
		 */
		public static function Main(
			array $arr=[],
			array $keys=[],
			bool $checkUnsecure=false
		): array {
			foreach ($arr AS $k => $v) {
				if (!is_array($arr[$k])) {
					$arr[$k] = self::ValidateAndFixString($v, $keys, $checkUnsecure);
				}
				else {
					$keys[$k] = $k;
					$arr[$k] = self::Main($arr[$k], $keys, $checkUnsecure);
					unset($keys[$k]);
				}
			}

			return $arr;
		}

		private static function ValidateAndFixString(
			string $str="",
			array $keys=[],
			bool $checkUnsecure=false
		): string {
			$str = self::PreventSqlInjection($str);
			$str = self::FixCustomParams($str, $keys);

			if ($checkUnsecure) {
				$str = self::CheckForUnsecureValues($str, 2);

			}

			return $str;
		}

		/**
		 * Prevent SQL Injection and XSS
		 */
		private static function PreventSqlInjection(
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
			$unsecureCount = 0;

			$str = urldecode($str);

			foreach (self::UnsecureVals AS $unsecureString) {
				if (strpos(strtolower($str), strtolower($unsecureString)) !== false) {
					$unsecureCount++;
				}
			}

			if ($minCount > 0 && $unsecureCount >= $minCount) {
				foreach (self::UnsecureVals AS $unsecureString) {
					$str = str_replace(strtolower($unsecureString), "_" . strtolower($unsecureString) . "_", strtolower($str));
				}
			}

			return $str;
		}

		/**
		 * Fix Values by Custom Params
		 */
		private static function FixCustomParams(
			string $str="",
			array $keys=[]
		): string {
			foreach ($keys AS $key) {
				switch ($key) {
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
