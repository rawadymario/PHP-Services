<?php
	namespace RawadyMario\Classes\Database;

    use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Helpers\DateHelper;

	class UserTokens extends Database{

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "user_tokens";
			$this->_key		= "id";

			$this->hideDeleted();
			$this->getInstance();
			
			if ($id > 0) {
				parent::load($id);
			}	
		}


		public function loadByToken($token) {
			$condition = "e.`token` = '$token'";

			$this->listAll($condition);
		}


		public function expired() {
			$expireOn = $this->row["expire_on"];
			if (is_null($expireOn)) {
				return false;
			}

			$expireOnStr = strtotime($expireOn);
			if ($expireOnStr >= time()) {
				return false;
			}
			
			return true;
		}


		public function loadSpecific($token, $userId) {
			$condition = "e.`token` = '$token' AND e.`user_id` = '$userId' AND (e.`expire_on` IS NULL OR e.`expire_on` >= '" . date(DateHelper::DATETIME_FORMAT_SAVE) . "')";

			$this->listAll($condition);
		}

		public function updateExpiry() {
			$this->update([
				"expire_on" => date(DateHelper::DATETIME_FORMAT_SAVE, time() + $this->row["length"])
			], "`id` = " . $this->row["id"]);
		}

	}