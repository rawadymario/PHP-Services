<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\CurrencyHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\NotNumericParamException;
	use RawadyMario\Helpers\CurrencyHelper;
	use RawadyMario\Helpers\TranslateHelper;
	use RawadyMario\Models\CurrencyPosition;

	final class CurrencyHelperTest extends TestCase {

		public function testAddCurrencyUsdPreWithSpaceSuccess(): void {
			$this->assertEquals(
				"$ 10",
				CurrencyHelper::AddCurrency(10, "$", CurrencyPosition::PRE, " ")
			);
		}

		public function testAddCurrencyUsdPreWithoutSpaceSuccess(): void {
			$this->assertEquals(
				"$10",
				CurrencyHelper::AddCurrency(10, "$", CurrencyPosition::PRE)
			);
		}

		public function testAddCurrencyUsdPostWithSpaceSuccess(): void {
			$this->assertEquals(
				"10 $",
				CurrencyHelper::AddCurrency(10, "$", CurrencyPosition::POST, " ")
			);
		}

		public function testAddCurrencyUsdPostWithoutSpaceSuccess(): void {
			$this->assertEquals(
				"10$",
				CurrencyHelper::AddCurrency(10, "$", CurrencyPosition::POST)
			);
		}

		public function testGetLbpAmountNonNumericValueFail(): void {
			$this->expectException(NotNumericParamException::class);
			$this->expectExceptionMessage(TranslateHelper::TranslateString("exception.NotNumericParam", null, [
				"::params::" => "amount"
			]));
			CurrencyHelper::GetLbpAmount("Mario");
		}

		public function testGetLbpAmountSuccess(): void {
			$this->assertEquals(
				10000,
				CurrencyHelper::GetLbpAmount(10000)
			);

			$this->assertEquals(
				10250,
				CurrencyHelper::GetLbpAmount(10050.04)
			);

			$this->assertEquals(
				10500,
				CurrencyHelper::GetLbpAmount(10250.01)
			);

			$this->assertEquals(
				10750,
				CurrencyHelper::GetLbpAmount(10650)
			);

			$this->assertEquals(
				10750,
				CurrencyHelper::GetLbpAmount(10750)
			);

			$this->assertEquals(
				11000,
				CurrencyHelper::GetLbpAmount(10751)
			);
		}

	}
