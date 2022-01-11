<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\CurrencyHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\CurrencyHelper;
	use RawadyMario\Models\CurrencyPosition;

	final class CurrencyHelperTest extends TestCase {
		
		public function testAddCurrencySuccess(): void {
			$this->assertEquals(
				"$ 10",
				CurrencyHelper::AddCurrency(10, "$", CurrencyPosition::PRE, " ")
			);
			
			$this->assertEquals(
				"$10",
				CurrencyHelper::AddCurrency(10, "$", CurrencyPosition::PRE)
			);
			
			$this->assertEquals(
				"10 $",
				CurrencyHelper::AddCurrency(10, "$", CurrencyPosition::POST, " ")
			);
			
			$this->assertEquals(
				"10$",
				CurrencyHelper::AddCurrency(10, "$", CurrencyPosition::POST)
			);
		}

		public function testGetLbpAmountSuccess(): void {
			// $this->assertEquals(
			// 	"$ 10",
			// 	CurrencyHelper::GetLbpAmount(10.125, 1, false)
			// );
		}
		
	}
	