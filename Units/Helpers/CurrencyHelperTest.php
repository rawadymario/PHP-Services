<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\CurrencyHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\NotNumericParamException;
	use RawadyMario\Helpers\CurrencyHelper;
	use RawadyMario\Helpers\TranslateHelper;
	use RawadyMario\Models\CurrencyPosition;

	final class CurrencyHelperTest extends TestCase {

		public function AddCurrencySuccessProvider() {
			return [
				"UsdPreWithSpace" => [
					"$ 10",
					[10, "$", CurrencyPosition::PRE, " "]
				],
				"UsdPreWithoutSpace" => [
					"$10",
					[10, "$", CurrencyPosition::PRE, ""]
				],
				"UsdPostWithSpace" => [
					"10 $",
					[10, "$", CurrencyPosition::POST, " "]
				],
				"UsdPostWithoutSpace" => [
					"10$",
					[10, "$", CurrencyPosition::POST, ""]
				],
			];
		}

		/**
		 * @dataProvider AddCurrencySuccessProvider
		 */
		public function testAddCurrencySuccess(
			string $expected,
			array $arguments
		): void {
			$this->assertEquals(
				$expected,
				CurrencyHelper::AddCurrency(
					$arguments[0],
					$arguments[1],
					$arguments[2],
					$arguments[3],
				)
			);
		}

		public function testGetLbpAmountNonNumericValueFail(): void {
			$this->expectException(NotNumericParamException::class);
			$this->expectExceptionMessage(TranslateHelper::TranslateString("exception.NotNumericParam", null, [
				"::params::" => "amount"
			]));
			CurrencyHelper::GetLbpAmount("Mario");
		}

		public function GetLbpAmountSuccessProvider() {
			return [
				[10000, 10000],
				[10250, 10050.04],
				[10500, 10250.01],
				[10750, 10650],
				[10750, 10750],
				[11000, 10751],
			];
		}

		/**
		 * @dataProvider GetLbpAmountSuccessProvider
		 */
		public function testGetLbpAmountSuccess(
			int $expected,
			float $argument
		): void {
			$this->assertEquals(
				$expected,
				CurrencyHelper::GetLbpAmount($argument)
			);
		}

	}
