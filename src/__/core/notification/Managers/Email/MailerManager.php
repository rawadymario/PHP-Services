<?php
	namespace RawadyMario\Classes\Core\Notification\Manager\Email;

	use RawadyMario\Classes\Database\User;
	use RawadyMario\Classes\Helpers\MediaHelper;
	use RawadyMario\Classes\Helpers\ViewHelper;

	class MailerManager {
		private const TENANT_NAME = CLIENT_NAME;
		private const TENANT_LOGO = LOGO_FULL_LD;
		private const TENANT_MAIN_COLOR = PRIMARY_COLOR;
		private const TENANT_YEAR = CLIENT_YEAR;
		private const TENANT_LINK = WEBSITE_ROOT;

		protected const MAIL_HOST = MAIL_HOST;
		protected const MAIL_PORT = MAIL_PORT;
		protected const MAIL_USERNAME = NOREPLY_EMAIL;
		protected const MAIL_PASSWORD = NOREPLY_EMAIL_PASS;
		protected const MAIL_ENCRYPTION = MAIL_ENCRYPTION;
		protected const MAIL_FROM_ADDRESS = NOREPLY_EMAIL;
		protected const MAIL_FROM_NAME = CLIENT_NAME;
		protected const MAIL_TESTING_EMAIL = TESTING_EMAIL;

		protected $user = null;

		protected $queueName = "";
		protected $payload = [];
		
		protected $to = [];
		protected $cc = [];
		protected $bcc = [];
		protected $subject = "";
		protected $body = "";
		protected $templateData = [];
		protected $attachments = [];
		protected $mainTemplate = "main.boxed_with_button";
		protected $template = "";
		
		protected $defaultUrl = "";
		
		public function __construct() {
			$this->SetDefaultUrl(self::TENANT_LINK);
		}

		public function Send() {
			// return [
			//     "status" => AppCode::SUCCESS,
			//     "message" => "Send Email Function Not Defined!"
			// ];
		}
		
		protected function SetDefaultValues() {
			$this->AppendTemplateData("tenant_name", self::TENANT_NAME);
			$this->AppendTemplateData("tenant_logo", MediaHelper::GetMediaFullPath(self::TENANT_LOGO));
			$this->AppendTemplateData("tenant_main_color", self::TENANT_MAIN_COLOR);
			$this->AppendTemplateData("tenant_year", self::TENANT_YEAR);

			if (!isset($this->templateData["full_name"])) {
				$this->templateData["full_name"] = '';
			}

			if (!isset($this->templateData["button_text"])) {
				$this->templateData["button_text"] = "Open Website";
			}
			if (!isset($this->templateData["url"])) {
				$this->templateData["url"] = $this->defaultUrl;
			}

			if ($this->user != null) {
				$fullname = implode(" ", [$this->user->row["first_name"], $this->user->row["last_name"]]);
				
				if (count($this->to) == 0) {
					$this->addRecepient($this->user->row["email"], $fullname);
				}

				if ($this->templateData["full_name"] == "") {
					$this->AppendTemplateData("full_name", $fullname);
				}
			}

			if (count($this->to) > 0) {
				if (!isset($this->templateData["full_name"]) || $this->templateData["full_name"] == "") {
					foreach ($this->to AS $email => $name) {
						$this->templateData["full_name"] = $name;
						break;
					}
				}
			}
		}

		protected function ValidateBeforeSend() : array {
			if ($this->mainTemplate == "") {
				return [
					"status" => ERROR,
					"message" => "Email Main Template is Required"
				];
			}
			
			if ($this->template == "") {
				return [
					"status" => ERROR,
					"message" => "Email Template is Required"
				];
			}

			if (!isset($this->to) || count($this->to) == 0) {
				return [
					"status" => ERROR,
					"message" => "No Email Recepient is Provided"
				];
			}

			return [];
		}

		protected function FixForNonProduction() {
			if (!IS_PROD_ENV) {
				$this->subject = ucwords(strtolower(ENV_NAME)) . " - " . $this->subject;

				$replaced = [];
				if (count($this->to) > 0) {
					$replaced[] = "To:" . implode(";", array_keys($this->to));
					$this->to = [
						self::MAIL_TESTING_EMAIL => self::MAIL_TESTING_EMAIL
					];
				}

				if (count($this->cc) > 0) {
					$replaced[] = "CC:" . implode(";", array_keys($this->cc));
					$this->cc = [];
				}

				if (count($this->bcc) > 0) {
					$replaced[] = "BCC:" . implode(";", array_keys($this->bcc));
					$this->bcc = [];
				}

				if (count($replaced) > 0) {
					$this->subject .= " [Replaced " . implode(" - ", $replaced) . "]";
				}
			}
		}


		protected function FixBody() {
			$this->body = ViewHelper::GetEmailTemplate($this->mainTemplate, $this->templateData);
			
			if ($this->template != "") {
				$emailBody = ViewHelper::GetEmailTemplate($this->template, $this->templateData);
				$this->body = str_replace("{{email_content}}", $emailBody, $this->body);
			}

			foreach ($this->templateData AS $_k => $_v) {
				$this->body = str_replace("{{" . $_k . "}}", $_v, $this->body);
			}
			$this->body = str_replace(["{{", "}}"], "", $this->body);
		}


		//BEGIN: Setters
		public function SetQueueName(string $str) : void {
			$this->queueName = $str;
		}
		
		public function SetPayload(array $payload) : void {
			$this->payload = $payload;
		}
		
		public function SetUser(User $user) {
			$this->user = $user;
		}

		public function AddAttachment($name, $path) {
			$this->attachments[] = [
				'name' => $name,
				'path' => $path
			];
		}

		public function AddAttachments($arr) {
			foreach ($arr AS $row) {
				$this->AddAttachment($row["name"], $row["path"]);
			}
		}

		public function SetMainTemplateBoxedWithButton() {
			$this->SetMainTemplate("main/boxed_with_button");
		}

		public function SetMainTemplateBoxed() {
			$this->SetMainTemplate("main/boxed");
		}

		protected function SetMainTemplate($templateName) {
			$this->mainTemplate = $templateName;
		}

		public function SetTemplate($templateName) {
			$this->template = $templateName;
		}

		public function SetTemplateData($templateData) {
			$this->templateData = $templateData;
		}

		public function AppendTemplateData($k, $v) {
			$this->templateData[$k] = $v;
		}

		public function SetSubject($subject) {
			$this->subject = $subject;
		}

		public function AddRecepient($email, $name="") {
			if (!isset($this->to[$email])) {
				if ($name == "") {
					$name = $email;
				}
	
				if (!isset($this->to)) {
					$this->to = [];
				}
				$this->to[$email] = $name;
			}
		}

		public function AddRecepients($arr) {
			foreach ($arr AS $row) {
				$this->AddRecepient($row["email"], (isset($row["name"]) ? $row["name"] : ""));
			}
		}

		public function ClearRecepients() {
			$this->to = [];
		}

		public function AddCc($email, $name="") {
			if (!isset( $this->cc[$email])) {
				if ($name == "") {
					$name = $email;
				}
	
				if (!isset($this->cc)) {
					$this->cc = [];
				}
				$this->cc[$email] = $name;
			}
		}

		public function AddCcs($arr) {
			foreach ($arr AS $row) {
				$this->AddCc($row["email"], (isset($row["name"]) ? $row["name"] : ""));
			}
		}

		public function ClearCcs() {
			$this->cc = [];
		}

		public function AddBcc($email, $name="") {
			if (!isset( $this->bcc[$email])) {
				if ($name == "") {
					$name = $email;
				}
				if (!isset($this->bcc)) {
					$this->bcc = [];
				}
				$this->bcc[$email] = $name;
			}
		}

		public function AddBccs($arr) {
			foreach ($arr AS $row) {
				$this->AddBcc($row["email"], (isset($row["name"]) ? $row["name"] : ""));
			}
		}

		public function ClearBccs() {
			$this->bcc = [];
		}

		public function SetDefaultUrl($url="") {
			$this->defaultUrl = $url;
		}
		//END: Setters

	}