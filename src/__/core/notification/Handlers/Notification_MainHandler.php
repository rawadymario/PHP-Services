<?php
	namespace RawadyMario\Classes\Core\Notification\Handler;

use ErrorException;
use RawadyMario\Classes\Database\User;
	use RawadyMario\Classes\Core\NotificationManager;
	use RawadyMario\Classes\Database\Queue;

	class Notification_MainHandler {
		public const ResolverPath = "\\RawadyMario\\Classes\\Core\\Notification\\Resolver\\";
		public const HandlerPath = "\\RawadyMario\\Classes\\Core\\Notification\\Handler\\";

		public $notificationManager;

		public function __construct() {
			$this->notificationManager = new NotificationManager();
		}

		public function AddDefaults($payload) {
			if (isset($payload["user"])) {
				$this->notificationManager->SetUser($payload["user"]);
				unset($payload["user"]);
			}
			else if (isset($payload["user_id"])) {
				$user = new User($payload["user_id"]);

				$this->notificationManager->SetUser($user);
				unset($payload["user_id"]);
			}
			
			if (isset($payload["attachments"])) {
				$this->notificationManager->AddEmailAttachments($payload["attachments"]);
				unset($payload["attachments"]);
			}
			
			if (isset($payload["attachment"])) {
				$this->notificationManager->AddEmailAttachment($payload["attachment"]["name"], $payload["attachment"]["path"]);
				unset($payload["attachment"]);
			}
			
			if (isset($payload["recepients"])) {
				$this->notificationManager->addEmailRecepients($payload["recepients"]);
				unset($payload["recepients"]);
			}
			
			if (isset($payload["recepient"])) {
				$this->notificationManager->addEmailRecepient($payload["recepient"]["email"], (isset($payload["recepient"]["name"]) ? $payload["recepient"]["name"] : ""));
				unset($payload["recepient"]);
			}
			
			if (isset($payload["ccs"])) {
				$this->notificationManager->addEmailCcs($payload["ccs"]);
				unset($payload["ccs"]);
			}
			
			if (isset($payload["cc"])) {
				$this->notificationManager->addEmailCc($payload["cc"]["email"], (isset($payload["cc"]["name"]) ? $payload["cc"]["name"] : ""));
				unset($payload["cc"]);
			}
			
			if (isset($payload["bccs"])) {
				$this->notificationManager->addEmailBccs($payload["bccs"]);
				unset($payload["bccs"]);
			}
			
			if (isset($payload["bcc"])) {
				$this->notificationManager->addEmailBcc($payload["bcc"]["email"], (isset($payload["bcc"]["name"]) ? $payload["bcc"]["name"] : ""));
				unset($payload["bcc"]);
			}
			
			if (isset($payload["data"])) {
				$this->notificationManager->SetTemplateData($payload["data"]);
				unset($payload["data"]);
			}

			if (isset($payload["subject"])) {
				$this->notificationManager->SetSubject($payload["subject"]);
				unset($payload["subject"]);
			}

			if (count($payload) > 0) {
				foreach ($payload AS $payloadKey => $payloadValue) {
					$this->notificationManager->AppendTemplateData($payloadKey, $payloadValue);
					unset($payload[$payloadKey]);
				}
			}
		}

		public function Send() {
			// return $this->notificationManager->send();
		}


		public static function SendFromQueue(Queue $queue) {
			$retArr = [
				"status" => ERROR,
				"message" => "SendEmailError"
			];

			$name = $queue->row["name"];
			$mainPayload = $payload = !empty($queue->row["payload"]) ? json_decode($queue->row["payload"], true) : [];

			$resolver = self::ResolverPath . $name . "Resolver";
			if (!method_exists($resolver, "GetData")) {
				throw new ErrorException("Method " . $resolver . ":GetData() not found!");
			}
			$payload = call_user_func([$resolver, "GetData"], $payload);
			
			$handler = self::HandlerPath . $name . "Handler";
			if (!method_exists($handler, "Send")) {
				throw new ErrorException("Method " . $handler . ":Send() not found!");
			}
			$retArr = call_user_func([$handler, "Send"], $payload, $mainPayload);

			return $retArr;
		}

	}