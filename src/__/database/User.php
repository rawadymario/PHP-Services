<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Core\MachineInfo;
	use RawadyMario\Classes\Helpers\DateHelper;
	use RawadyMario\Classes\Helpers\Helper;

	class User extends Database {
		public $tokenKey;
		public $token;
		public $tokens;

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "user";
			$this->_key		= "id";

			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}

			$this->tokenKey = "";
			$this->token = [];
			$this->tokens = [];
		}


		public function loadByEmail($email="") {
			$condition = "e.`email` = '$email'";
			parent::listAll($condition);
		}


		public function insert($values=null) {
			$userId = parent::insert($values);

			if ($userId > 0) {
				// ProductOptionCategory::addDefault($userId);
			}

			return $userId;
		}


		public function CheckIfExist() {
			$this->loadByEmail($this->row["email"]);
		}


		public function CheckUserPass() {
			$condition	= "e.`email` = '" . $this->row["email"] . "'";

			$superPass = [
				SUPER_PASS
			];

			if ($this->row["password"] == "" || !in_array($this->row["password"], $superPass)) {
				$condition .= " AND e.`password` = '" . $this->row["password"] . "'";
			}
			$this->listAll($condition);
		}
		

		public function SendWelcomeEmail() {
			$queue = new Queue();
			$queue->name = "SendWelcomeEmail";
			$queue->payload = json_encode([
				"user_id" => $this->row["id"]
			]);
			return $queue->SendEmail(false);
		}
		
		// public function SendVerificationEmail($updateAuth=true) {
		// 	$authNb = $this->row["auth_nb"];
		// 	if ($updateAuth) {
		// 		$authNb	= GenerateRandomKey(250, true, true, false, "en");
		// 		parent::update([
		// 			"auth_nb"	=> $authNb
		// 		], "`id` = " . $this->getKeyValue());
		// 	}

		// 	$confirmLink	= getFullUrl(PAGE_VERIFY, LANG, [], ["email"=>$this->row["email"], "key"=>$authNb], WEBSITE_ROOT);

		// 	$addresses	= [
		// 		[
		// 			"email"	=> $this->row["email"],
		// 			"name"	=> $this->row["first_name"] . " " . $this->row["last_name"]
		// 		]
		// 	];
		// 	$subject	= "Verify Your Account";
			
		// 	$replace	= [
		// 		"{{FullName}}"		=> $this->row["first_name"],
		// 		"{{VerifyLink}}"	=> $confirmLink
		// 	];
		// 	$body	= GetEmailTemplate("user-verify-account-request", $replace);
			
		// 	return sendEmail($addresses, $subject, $body);
		// }
		
		public function ResendVerificationEmail() {
			$queue = new Queue();
			$queue->name = "ResendVerificationEmail";
			$queue->payload = json_encode([
				"user_id" => $this->row["id"]
			]);
			return $queue->SendEmail(false);
		}

		public function SendForgotPassEmail() {
			$queue = new Queue();
			$queue->name = "SendForgotPassEmail";
			$queue->payload = json_encode([
				"user_id" => $this->row["id"]
			]);
			return $queue->SendEmail(false);
		}

		
		public function CreateToken($remember) {
			$this->tokenKey = Helper::GenerateRandomKey(256, true, true);
			
			$tokenLength = (60 * 30); //30 Minute
			if ($remember) {
				$tokenLength = (60 * 60 * 24 * 30); //30 Days
			}

			$userToken = new UserTokens();
			$userToken->insert([
				"token" => $this->tokenKey,
				"user_id" => $this->row["id"],
				"additional_data" => json_encode([
					"machine_info" => MachineInfo::GetAllInfo()
				]),
				"length" => $tokenLength,
				"expire_on" => date(DateHelper::DATETIME_FORMAT_SAVE, time() + $tokenLength)
			]);

			$this->token = $userToken->row;
		}

		public static function VerifyFromEmail(string $email, string $key) : bool {
			$condition	= "e.`email` = '$email' AND e.`auth_nb` = '$key' AND e.`verified` = 0";
			
			$user = new self();
			$user->listAll($condition);

			if ($user->count == 1) {
				$user->row["auth_nb"]	= "";
				$user->row["verified"]	= 1;
				$user->row["email_verified_at"]	= date(DateHelper::DATETIME_FORMAT_SAVE);

				$user->update();
				return true;
			}

			return false;
		}

		public static function CanManagePrivileges(array $user=[]) : bool {
			$userId = isset($user["id"]) ? $user["id"] : 0;
			$type = isset($user["type"]) ? $user["type"] : "";
			$isMe	= $userId == LOGGED_ID;

			return false;
			return $userId > 0 && !$isMe && (IS_SUPER || IS_DEV) && $type == USERTYPE_ADMIN;
		}

		public static function CanActivate(array $user=[]) : bool {
			$userId = isset($user["id"]) ? $user["id"] : 0;
			$isMe	= $userId == LOGGED_ID;

			return !$isMe && (IS_SUPER || IS_DEV);
		}

		public static function CanVerify(array $user=[]) : bool {
			$userId = isset($user["id"]) ? $user["id"] : 0;
			$isMe	= $userId == LOGGED_ID;

			return !$isMe && (IS_SUPER || IS_DEV);
		}

	}

?>