<?php
	namespace RawadyMario\Classes\Core\Notification\Resolver;

	use RawadyMario\Classes\Helpers\Helper;

	class SendPaymentSuccessEmailToUserResolver {
		
		public static function GetData(array $payload) {
			$payload["amount"] = Helper::AddCurrency($payload["amount"] ?? "");
			$payload["remote_id"] = $payload["remote_id"] ?? "N/A";
			$payload["subject"] = "Payment Confirmed";

			return $payload;
		}

	}