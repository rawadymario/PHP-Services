<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\ValidatorHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\ValidatorHelper;

	final class ValidatorHelperTest extends TestCase {

		public function testValidEmailFail(): void {
			$this->assertFalse(
				ValidatorHelper::ValidEmail("Test Email")
			);

			$this->assertFalse(
				ValidatorHelper::ValidEmail("test_email@hotmail")
			);

			$this->assertFalse(
				ValidatorHelper::ValidEmail("test_email@hotmail.c")
			);

			$this->assertFalse(
				ValidatorHelper::ValidEmail("test_email.com")
			);
		}

		public function testValidEmailSuccess(): void {
			$this->assertTrue(
				ValidatorHelper::ValidEmail("test_email@hotmail.co")
			);

			$this->assertTrue(
				ValidatorHelper::ValidEmail("test_email@hotmail.com")
			);

			$this->assertTrue(
				ValidatorHelper::ValidEmail("test_email@hotmail.co.uk")
			);

			$this->assertTrue(
				ValidatorHelper::ValidEmail("test_email@hotmail.com.lb")
			);
		}

		public function testValidPhoneNbFail(): void {
			$this->assertFalse(
				ValidatorHelper::ValidPhoneNb("Not a Mobile")
			);

			$this->assertFalse(
				ValidatorHelper::ValidPhoneNb("Special Characters !@#$%^&*()_-+=")
			);
		}

		public function testValidPhoneNbSuccess(): void {
			$this->assertTrue(
				ValidatorHelper::ValidPhoneNb("03/333333")
			);

			$this->assertTrue(
				ValidatorHelper::ValidPhoneNb("03-333333")
			);

			$this->assertTrue(
				ValidatorHelper::ValidPhoneNb("+961 3 333333")
			);
		}

		public function testValidNumberFail(): void {
			$this->assertFalse(
				ValidatorHelper::ValidNumber("Not a NUmber")
			);

			$this->assertFalse(
				ValidatorHelper::ValidNumber("Special Characters !@#$%^&*()_-+=")
			);

			$this->assertFalse(
				ValidatorHelper::ValidNumber("123456789a")
			);

			$this->assertFalse(
				ValidatorHelper::ValidNumber("123456789$")
			);
		}

		public function testValidNumberSuccess(): void {
			$this->assertTrue(
				ValidatorHelper::ValidNumber("0123456789")
			);
		}

		public function testCleanPhoneNbSuccess(): void {
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
					ValidatorHelper::CleanPhoneNb(" 03{$char}333333 ")
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
					ValidatorHelper::CleanPhoneNb(" 03{$char}333333 ")
				);
			}
		}

	}
