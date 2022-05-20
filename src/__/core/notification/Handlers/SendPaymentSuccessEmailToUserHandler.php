<?php
	namespace RawadyMario\Classes\Core\Notification\Handler;

	class SendPaymentSuccessEmailToUserHandler {
		
		public static function Send(array $payload=[], ?array $mainPayload=[]) {
			if (is_null($mainPayload) || count($mainPayload) == 0) {
				$mainPayload = $payload;
			}

			$handler = new Notification_MainHandler();
            $handler->AddDefaults($payload);
			
			$handler->notificationManager->haveEmail = true;
			$handler->notificationManager->SetEmailMainTemplateBoxed();
			$handler->notificationManager->SetTemplate("payment/SendPaymentSuccessEmailToUser");
			
			$handler->notificationManager->SetQueueName("SendPaymentSuccessEmailToUser");
			$handler->notificationManager->SetPayload($mainPayload);

			return $handler->notificationManager->Send();
		}

	}