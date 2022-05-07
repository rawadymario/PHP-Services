<?php
	//To Run: .\vendor/bin/phpunit .\Units\Automated\Helpers\ValidatorHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\InvalidEmailException;
	use RawadyMario\Exceptions\InvalidNumberException;
	use RawadyMario\Exceptions\InvalidPasswordCharactersException;
	use RawadyMario\Exceptions\InvalidPasswordLengthException;
	use RawadyMario\Exceptions\InvalidPhoneNumberException;
	use RawadyMario\Exceptions\InvalidUsernameCharactersException;
	use RawadyMario\Exceptions\InvalidUsernameLengthException;
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Helpers\TranslateHelper;
	use RawadyMario\Helpers\ValidatorHelper;

	final class ValidatorHelperTest extends TestCase {

		public function ValidateEmailThrowErrorProvider() {
			return [
				[
					InvalidEmailException::class,
					"exception.InvalidEmail",
					"Test Email"
				],
				[
					InvalidEmailException::class,
					"exception.InvalidEmail",
					"test_email@hotmail"
				],
				[
					InvalidEmailException::class,
					"exception.InvalidEmail",
					"test_email@hotmail.c"
				],
				[
					InvalidEmailException::class,
					"exception.InvalidEmail",
					"test_email.com"
				],
			];
		}

		/**
		 * @dataProvider ValidateEmailThrowErrorProvider
		 */
		public function testValidateEmailThrowError(
			$exception,
			string $exceptionMessage,
			string $argument
		): void {
			$this->expectException($exception);
			$this->expectExceptionMessage(TranslateHelper::Translate($exceptionMessage));
			ValidatorHelper::ValidateEmail($argument);
		}

		public function ValidateEmailSuccessProvider() {
			return [
				["test_email@hotmail.co"],
				["test_email@hotmail.com"],
				["test_email@hotmail.co.uk"],
				["test_email@hotmail.com.lb"],
			];
		}

		/**
		 * @dataProvider ValidateEmailSuccessProvider
		 */
		public function testValidateEmailSuccess(
			string $argument
		): void {
			$this->assertTrue(
				ValidatorHelper::ValidateEmail($argument)
			);
		}

		public function ValidatePhoneNumberThrowErrorProvider() {
			return [
				[
					InvalidPhoneNumberException::class,
					"exception.InvalidPhoneNumber",
					"Not a Mobile"
				],
				[
					InvalidPhoneNumberException::class,
					"exception.InvalidPhoneNumber",
					"Special Characters !@#$%^&*()_-+="
				],
			];
		}

		/**
		 * @dataProvider ValidatePhoneNumberThrowErrorProvider
		 */
		public function testValidatePhoneNumberThrowError(
			$exception,
			string $exceptionMessage,
			string $argument
		): void {
			$this->expectException($exception);
			$this->expectExceptionMessage(TranslateHelper::Translate($exceptionMessage));
			ValidatorHelper::ValidatePhoneNumber($argument);
		}

		public function ValidatePhoneNumberSuccessProvider() {
			return [
				["03/333333"],
				["03-333333"],
				["+961 3 333333"],
			];
		}

		/**
		 * @dataProvider ValidatePhoneNumberSuccessProvider
		 */
		public function testValidatePhoneNumberSuccess(
			string $argument
		): void {
			$this->assertTrue(
				ValidatorHelper::ValidatePhoneNumber($argument)
			);
		}

		public function ValidateNumberThrowErrorProvider() {
			return [
				[
					InvalidNumberException::class,
					"exception.InvalidNumber",
					"Not a NUmber"
				],
				[
					InvalidNumberException::class,
					"exception.InvalidNumber",
					"Special Characters !@#$%^&*()_-+="
				],
				[
					InvalidNumberException::class,
					"exception.InvalidNumber",
					"123456789a"
				],
				[
					InvalidNumberException::class,
					"exception.InvalidNumber",
					"123456789$"
				],
			];
		}

		/**
		 * @dataProvider ValidateNumberThrowErrorProvider
		 */
		public function testValidateNumberThrowError(
			$exception,
			string $exceptionMessage,
			string $argument
		): void {
			$this->expectException($exception);
			$this->expectExceptionMessage(TranslateHelper::Translate($exceptionMessage));
			ValidatorHelper::ValidateNumber($argument);
		}

		public function testValidateNumberSuccess(): void {
			$this->assertTrue(
				ValidatorHelper::ValidateNumber("0123456789")
			);
		}

		public function testCleanPhoneNumberSuccess(): void {
			$acceptedChars = [
				"-",
				"/",
				"\\",
				",",
				".",
				"|",
				"%20", // encoded space character
			];
			foreach ($acceptedChars AS $char) {
				$this->assertEquals(
					"03333333",
					ValidatorHelper::CleanPhoneNumber(" 03{$char}333333 ")
				);
			}

			$nonAcceptedChars = [
				":",
				";",
				"*",
			];
			foreach ($nonAcceptedChars AS $char) {
				$this->assertEquals(
					"03{$char}333333",
					ValidatorHelper::CleanPhoneNumber(" 03{$char}333333 ")
				);
			}
		}

		public function testValidateUsernameLengthBelowMinimumThrowError(): void {
			$this->expectException(InvalidUsernameLengthException::class);
			$this->expectExceptionMessage(TranslateHelper::Translate("exception.InvalidUsernameLength"));
			ValidatorHelper::ValidateUsername(Helper::GenerateRandomKey(5, true, true));
		}

		public function testValidateUsernameLengthAboveMaximumThrowError(): void {
			$this->expectException(InvalidUsernameLengthException::class);
			$this->expectExceptionMessage(TranslateHelper::Translate("exception.InvalidUsernameLength"));
			ValidatorHelper::ValidateUsername(Helper::GenerateRandomKey(21, true, true));
		}

		public function testValidateUsernameCharactersThrowError_01(): void {
			$this->expectException(InvalidUsernameCharactersException::class);
			$this->expectExceptionMessage(TranslateHelper::Translate("exception.InvalidUsernameCharacters"));
			ValidatorHelper::ValidateUsername(Helper::GenerateRandomKey(12, false, false, true));
		}

		public function testValidateUsernameSuccess(): void {
			$this->assertTrue(
				ValidatorHelper::ValidateUsername("rawadymario")
			);
			$this->assertTrue(
				ValidatorHelper::ValidateUsername("rawady_mario")
			);
			$this->assertTrue(
				ValidatorHelper::ValidateUsername("Rawady_Mario")
			);
			$this->assertTrue(
				ValidatorHelper::ValidateUsername("mario_007")
			);
			$this->assertTrue(
				ValidatorHelper::ValidateUsername("Rawady_Mario_007")
			);
		}

		public function testValidatePasswordLengthBelowMinimumThrowError(): void {
			$this->expectException(InvalidPasswordLengthException::class);
			$this->expectExceptionMessage(TranslateHelper::Translate("exception.InvalidPasswordLength"));
			ValidatorHelper::ValidatePassword(Helper::GenerateRandomKey(5, true, true, true));
		}

		public function testValidatePasswordCharactersThrowError(): void {
			$this->expectException(InvalidPasswordCharactersException::class);
			$this->expectExceptionMessage(TranslateHelper::Translate("exception.InvalidPasswordCharacters"));
			ValidatorHelper::ValidatePassword(Helper::GenerateRandomKey(18, true, true, false));
		}

		public function testValidatePasswordSuccess(): void {
			$this->assertTrue(
				ValidatorHelper::ValidatePassword("MarioRawady123$%^")
			);
		}

	}
