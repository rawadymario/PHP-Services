<?php
	namespace RawadyMario\Classes\Core\Notification\Handler;

	class SendPaymentSuccessEmailToAdminHandler {
		
		public static function Send(array $payload=[], ?array $mainPayload=[]) {
			if (is_null($mainPayload) || count($mainPayload) == 0) {
				$mainPayload = $payload;
			}

			$handler = new Notification_MainHandler();
            $handler->AddDefaults($payload);
			
			$handler->notificationManager->haveEmail = true;
			$handler->notificationManager->SetEmailMainTemplateBoxed();
			$handler->notificationManager->SetTemplate("payment/SendPaymentSuccessEmailToAdmin");
			
			$handler->notificationManager->SetQueueName("SendPaymentSuccessEmailToAdmin");
			$handler->notificationManager->SetPayload($mainPayload);

			$handler->notificationManager->addEmailRecepient(ADMIN_EMAIL, "Admin");
			return $handler->notificationManager->Send();
		}

	}