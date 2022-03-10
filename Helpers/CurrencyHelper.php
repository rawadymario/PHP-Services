<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Models\CurrencyPosition;

	class CurrencyHelper {


		/**
		 * Add currency sign to the given value
		 */
		public static function AddCurrency(
			$value,
			string $currency="",
			string $position=CurrencyPosition::POST,
			string $separator=""
		): string {
			if ($currency != "") {
				if ($position === CurrencyPosition::PRE) {
					return $currency . $separator . $value;
				}

				if ($position === CurrencyPosition::POST) {
					return $value . $separator . $currency;
				}
			}

			return strval($value);
		}


		/**
		 * Ceil to the nearest LBP value (multiple of 0.25)
		 */
		public static function GetLbpAmount(
			$amount,
			int $decimalPlaces=2
		): float {
			if (!is_numeric($amount)) {
				return 0;
			}
			return Helper::ConvertToDec((ceil($amount / 250) * 250), $decimalPlaces);
		}


	}