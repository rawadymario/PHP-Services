<?php
	namespace RawadyMario\Classes\Core\Notification\Manager\Email;

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	use RawadyMario\Classes\Core\MachineInfo;
	use RawadyMario\Classes\Helpers\DateHelper;
	use RawadyMario\Classes\Helpers\Helper;
	use RawadyMario\Classes\Database\Logs\EmailLog;

	class PhpMailerManager extends MailerManager {
		
		public function __construct() {
			parent::__construct();
		}

		public function Send() : array {
			$this->setDefaultValues();
			$retArr = $this->ValidateBeforeSend();
			
			if (count($retArr) > 0) {
				return $retArr;
			}

			$mail = new PHPMailer(true); //true enables exceptions
			try {
				$this->FixBody();

				$emailLog = new EmailLog();
				$emailLog->row["name"] = $this->queueName;
				$emailLog->row["payload"] = count($this->payload) > 0 ? json_encode($this->payload) : "";

				if (!Helper::ObjectNullOrEmpty($this->user)) {
					$emailLog->row["user_id"] = $this->user->row["id"];
				}
				$emailLog->row["from"] = self::MAIL_FROM_ADDRESS;
				$emailLog->row["to"] = count($this->to) > 0 ? json_encode($this->to) : "";
				$emailLog->row["cc"] = count($this->cc) > 0 ? json_encode($this->cc) : "";
				$emailLog->row["bcc"] = count($this->bcc) > 0 ? json_encode($this->bcc) : "";
				$emailLog->row["subject"] = $this->subject;
				$emailLog->row["body"] = $this->body;
				$emailLog->row["machine_info"] = json_encode(MachineInfo::GetAllInfo());
				$emailLog->row["created_on"] = date(DateHelper::DATETIME_FORMAT_SAVE);
				$emailLog->insert();

				$this->FixForNonProduction();
				
				$mail->SMTPDebug = 0; //4: Enables Debugging
				$mail->isSMTP();
				$mail->SMTPAuth = true;
				$mail->SMTPSecure = self::MAIL_ENCRYPTION;
				$mail->Port = self::MAIL_PORT;
				$mail->Host = self::MAIL_HOST;
				$mail->Username = self::MAIL_USERNAME;
				$mail->Password = self::MAIL_PASSWORD;

				$mail->IsHTML(true);
				$mail->SetFrom(self::MAIL_FROM_ADDRESS, self::MAIL_FROM_NAME);

				foreach ($this->to AS $email => $name) {
					$mail->addAddress($email, $name);
				}

				foreach ($this->cc AS $email => $name) {
					$mail->addCC($email, $name);
				}

				foreach ($this->bcc AS $email => $name) {
					$mail->addBcc($email, $name);
				}

				// $mail->addReplyTo($email, $name);

				if(count($this->attachments) > 0) {
					foreach ($this->attachments as $att) {
						$mail->addAttachment($att["path"], $att["name"]);
					}
				}   

				$mail->Subject	= $this->subject;
				$mail->Body		= $this->body;
				// $mail->AltBody  = "Plain Text Version of the Message";

				if ($mail->send()) {
					$retArr = [
						"status" => SUCCESS,
						"message" => "SendEmailSuccess"
					];
				}
				else {
					$msg = _text("SendEmailError") . " Mailer Error: " . $mail->ErrorInfo;

					$retArr = [
						"status" => ERROR,
						"message" => $msg
					];
				}
			} catch (Exception $e) {
				$retArr = [
					"status" => ERROR,
					"message" => $e->errorMessage()
				];
			}
			
			if (!Helper::ObjectNullOrEmpty($emailLog)) {
				$emailLog->update([
					"status" => $retArr["status"],
					"response" => json_encode($retArr),
				]);
			}

			return $retArr;
		}

	}