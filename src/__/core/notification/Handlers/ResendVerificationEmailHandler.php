<?php
	namespace RawadyMario\Classes\Core\Notification\Handler;

	class ResendVerificationEmailHandler {
		
		public static function Send(array $payload=[], ?array $mainPayload=[]) {
			if (is_null($mainPayload) || count($mainPayload) == 0) {
				$mainPayload = $payload;
			}

			$handler = new Notification_MainHandler();
            $handler->AddDefaults($payload);
			
			$handler->notificationManager->haveEmail = true;
			$handler->notificationManager->SetEmailMainTemplateBoxedWithButton();
			$handler->notificationManager->SetTemplate("user/ResendVerificationEmail");
			
			$handler->notificationManager->SetQueueName("ResendVerificationEmail");
			$handler->notificationManager->SetPayload($mainPayload);
			
			return $handler->notificationManager->Send();
		}

	}