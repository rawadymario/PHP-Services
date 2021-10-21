<?php
	namespace RawadyMario\Helpers;


	class CurrencyHelper {


		/**
		 * Add currency sign to the given value
		 */
		public static function AddCurrency($value, string $currency="") {
			if ($currency != "") {
				$value .= $currency;
			}

			return $value;
		}


		/**
		 * Ceil to the nearest LBP value (multiple of 0.25)
		 */
		public static function GetLbpAmount($amount, int $decimalPlaces=2, bool $format=false) {
			return Helper::ConvertToDec((ceil($amount / 250) * 250), $decimalPlaces, $format);
		}

		
	}