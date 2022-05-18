<?php
	namespace RawadyMario\Classes\Core\Notification\Resolver;

	use RawadyMario\Classes\Database\User;
	use RawadyMario\Classes\Helpers\Helper;

	class SendPaymentSuccessEmailToAdminResolver {
		
		public static function GetData(array $payload) {
			$userId = Helper::ConvertToInt($payload["user_id"] ?? 0);
			unset($payload["user_id"]);
			
			$user = new User($userId);
			
			$user_info = "N/A";
			if ($user->count > 0) {
				$user_info = Helper::ImplodeArrStr([$user->row["first_name"], $user->row["last_name"]], " ") . " (" . $user->row["email"] . ")";
			}

			$payload["amount"] = Helper::AddCurrency($payload["amount"] ?? "");
			$payload["remote_id"] = $payload["remote_id"] ?? "N/A";
			$payload["user_info"] = $user_info;
			$payload["subject"] = "New Successful Payment";

			return $payload;
		}

	}