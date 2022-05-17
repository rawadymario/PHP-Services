<?php
	namespace RawadyMario\Accounting\Helpers;

	use RawadyMario\Accounting\Models\CurrencyPosition;
	use RawadyMario\Exceptions\NotNumericParamException;
	use RawadyMario\Helpers\Helper;

	class Currency {


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
				throw new NotNumericParamException("amount");
			}
			return Helper::convert_to_dec((ceil($amount / 250) * 250), $decimalPlaces);
		}


	}