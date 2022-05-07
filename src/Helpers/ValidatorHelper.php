<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Exceptions\InvalidEmailException;
	use RawadyMario\Exceptions\InvalidNumberException;
	use RawadyMario\Exceptions\InvalidPasswordCharactersException;
	use RawadyMario\Exceptions\InvalidPasswordLengthException;
	use RawadyMario\Exceptions\InvalidPhoneNumberException;
	use RawadyMario\Exceptions\InvalidUsernameCharactersException;
	use RawadyMario\Exceptions\InvalidUsernameLengthException;

	class ValidatorHelper {


		/**
		 * Check if a valid email address format is given
		 */
		public static function ValidateEmail(
			string $str
		): bool {
			$pattern = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
			if (!preg_match($pattern, $str)) {
				throw new InvalidEmailException();
			}
			return true;
		}


		/**
		 * Check if a valid phone number format is given
		 */
		public static function ValidatePhoneNumber(
			string $str
		): bool {
			$pattern = '/^[\+]?[0-9]+$/';
			if (!preg_match($pattern, self::CleanPhoneNumber($str))) {
				throw new InvalidPhoneNumberException();
			}
			return true;
		}


		/**
		 * Check if a valid mobile format is given
		 */
		public static function ValidateNumber(
			string $str
		): bool {
			$pattern = '/^[0-9]+$/';
			if (!preg_match($pattern, $str)) {
				throw new InvalidNumberException();
			}
			return true;
		}


		/**
		 * Clean a given string to be a valid phone nb
		 */
		public static function CleanPhoneNumber(
			string $str
		): string {
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
		 * 	- 6 to 20 Characters
		 * 	- English Small Letters
		 *  - English Capital Letters
		 *  - English Numbers
		 *  - Dash(-) or Underscore(_)
		 */
		public static function ValidateUsername(
			string $str
		): bool {
			if (strlen($str) < 6 || strlen($str) > 20) {
				throw new InvalidUsernameLengthException();
			}
			$pattern = '/^[a-zA-Z0-9-_.]+$/';
			if (!preg_match($pattern, $str)) {
				throw new InvalidUsernameCharactersException();
			}
			return true;
		}


		/**
		 * Check if a valid password format is given
		 * Must have at least:
		 * 	1 English Small letter
		 * 	1 English Capital letter
		 * 	1 Number
		 * 	1 Special Character
		 */
		public static function ValidatePassword(
			string $str,
			int $length=0
		): bool {
			if ($length == 0) {
				$length = strlen($str);
			}

			if ($length < 8) {
				throw new InvalidPasswordLengthException();
			}

			$pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[~!@#$%\^&*()\-_=+\|\[{\]};:\'",<.>\/?]).+$/';
			if (!preg_match($pattern, $str)) {
				throw new InvalidPasswordCharactersException();
			}

			return true;
		}

	}
