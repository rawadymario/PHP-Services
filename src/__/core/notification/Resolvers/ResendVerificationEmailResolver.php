<?php
	namespace RawadyMario\Classes\Core\Notification\Resolver;

	use RawadyMario\Classes\Database\User;
	use RawadyMario\Classes\Helpers\Helper;

	class ResendVerificationEmailResolver {
		
		public static function GetData(array $payload) {
			$userId = $payload["user_id"];

			$user = new User($userId);

			$authNb	= Helper::GenerateRandomKey(256, true, true, false, "en");
			$user->update([
				"auth_nb"	=> $authNb
			]);

			$confirmLink = getFullUrl(PAGE_VERIFY, LANG, [], ["email"=>$user->row["email"], "key"=>$authNb], WEBSITE_ROOT);
			$subject = "Verify Your Account";

			$payload["button_text"] = "Verify My Account";
			$payload["url"] = $confirmLink;
			$payload["subject"] = $subject;

			return $payload;
		}

	}