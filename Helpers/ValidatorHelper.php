<?php
	namespace RawadyMario\Helpers;

	class ValidatorHelper {


		/**
		 * Check if a valid email address format is given
		 */
		public static function ValidEmail(string $str): bool {
			$pattern = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
			return preg_match($pattern, $str);
		}


		/**
		 * Check if a valid phone number format is given
		 */
		public static function ValidPhoneNb(string $str): bool {
			$pattern = '/^[\+]?[0-9]+$/';
			return preg_match($pattern, self::CleanPhoneNb($str));
		}


		/**
		 * Check if a valid mobile format is given
		 */
		public static function ValidNumber(string $str): bool {
			$pattern = '/^[0-9]+$/';
			return preg_match($pattern, $str);
		}


		/**
		 * Clean a given string to be a valid phone nb
		 */
		public static function CleanPhoneNb(string $str): string {
			if ($str != "") {
				$str = urldecode($str);
				$str = trim($str);
				$str = str_replace(array("-", "/", "\\", ",", ".", "|", " "), "", $str);
			}
			return $str;
		}


		/**
		 * Check if a valid username format is give
		 * Must contain only:
		 * 	- Small English Letters
		 *  - Capital English Letters
		 *  - English Numbers
		 *  - Dash(-) or Underscore(_)
		 */
		public static function ValidUsername(string $str): bool {
			if (strlen($str) < 6 || strlen($str) > 20) {
				return false;
			}

			$pattern = '/^[a-zA-Z0-9-_.]+$/';
			return preg_match($pattern, $str);
		}


		/**
		 * Check if a valid password format is given
		 * Must have at least:
		 * 	1 Small letter
		 * 	1 Capital letter
		 * 	1 Number
		 * 	1 Special Character
		 */
		public static function ValidPassword(string $str, int $length=0): bool {
			if ($length == 0) {
				$length = strlen($str);
			}

			if ($length < 8) {
				return false;
			}

			$pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[~!@#$%\^&*()\-_=+\|\[{\]};:\'",<.>\/?]).+$/';
			return preg_match($pattern, $str);
		}


	}
