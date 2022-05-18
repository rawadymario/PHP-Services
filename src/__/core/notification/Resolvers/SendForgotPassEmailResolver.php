<?php
	namespace RawadyMario\Classes\Core\Notification\Resolver;

	use RawadyMario\Classes\Database\User;
	use RawadyMario\Classes\Helpers\Helper;

	class SendForgotPassEmailResolver {
		
		public static function GetData(array $payload) {
			$userId = $payload["user_id"];

			$user = new User($userId);

			$authNb	= Helper::GenerateRandomKey(256, true, true, false, "en");
			$user->update([
				"auth_nb"	=> $authNb
			]);

			$confirmLink = getFullUrl(PAGE_RESETPASS, LANG, [], ["email"=>$user->row["email"], "key"=>$authNb], WEBSITE_ROOT);
			$subject = "Reset Your Password";

			$payload["button_text"] = "Reset My Password";
			$payload["url"] = $confirmLink;
			$payload["subject"] = $subject;

			return $payload;
		}

	}