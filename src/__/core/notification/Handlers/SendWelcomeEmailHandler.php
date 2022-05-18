<?php
	namespace RawadyMario\Classes\Core\Notification\Handler;

	class SendWelcomeEmailHandler {
		
		public static function Send(array $payload=[], ?array $mainPayload=[]) {
			if (is_null($mainPayload) || count($mainPayload) == 0) {
				$mainPayload = $payload;
			}

			$handler = new Notification_MainHandler();
            $handler->AddDefaults($payload);
			
			$handler->notificationManager->haveEmail = true;
			$handler->notificationManager->SetEmailMainTemplateBoxedWithButton();
			$handler->notificationManager->SetTemplate("user/SendWelcomeEmail");
			
			$handler->notificationManager->SetQueueName("SendWelcomeEmail");
			$handler->notificationManager->SetPayload($mainPayload);
			
			return $handler->notificationManager->Send();
		}

	}