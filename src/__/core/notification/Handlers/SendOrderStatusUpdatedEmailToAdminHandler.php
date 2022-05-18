<?php
	namespace RawadyMario\Classes\Core\Notification\Handler;

	class SendOrderStatusUpdatedEmailToAdminHandler {
		
		public static function Send(array $payload=[], ?array $mainPayload=[]) {
			if (is_null($mainPayload) || count($mainPayload) == 0) {
				$mainPayload = $payload;
			}

			$handler = new Notification_MainHandler();
            $handler->AddDefaults($payload);
			
			$handler->notificationManager->haveEmail = true;
			$handler->notificationManager->SetEmailMainTemplateBoxedWithButton();
			$handler->notificationManager->SetTemplate("order/SendOrderStatusUpdatedEmailToAdmin");
			
			$handler->notificationManager->SetQueueName("SendOrderStatusUpdatedEmailToAdmin");
			$handler->notificationManager->SetPayload($mainPayload);

			$handler->notificationManager->addEmailRecepient(ADMIN_EMAIL, "Admin");
			return $handler->notificationManager->Send();
		}

	}