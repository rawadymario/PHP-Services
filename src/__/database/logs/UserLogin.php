<?php
	namespace RawadyMario\Classes\Database\Logs;

	use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Core\MachineInfo;
	use RawadyMario\Classes\Helpers\DateHelper;

	class UserLoginLog extends Database {
	
	
		public function __construct($id=0) {
			parent::__construct();

			$this->_database	= DB_LOGS;
			$this->_table		= "user_login";
			$this->_key			= "id";

			$this->getInstance();

			$this->clearAutoOrder();
			$this->hideDeleted();
			
			$this->autoSaveCreate	= false;
			$this->autoSaveUpdate	= false;

			if ($id > 0) {
				parent::load($id);
			}
		}
		

		public static function logLogin($userId) {
			$l = new UserLoginLog();
			$l->row["user_id"]		= $userId;
			$l->row["machine_info"] = json_encode(MachineInfo::GetAllInfo());
			$l->row["login_date"]	= date(DateHelper::DATETIME_FORMAT_SAVE);
			$l->insert();
		}

		public static function logLogout($userId) {
			$condition  = "e.`user_id` = $userId AND e.`logout_date` IS NULL";

			$l = new UserLoginLog();
			$l->orderBy("e.`id` DESC");
			$l->limit(0, 1);
			$l->listAll($condition);

			if ($l->count > 0) {
				$l->update([
					"logout_date" => date(DateHelper::DATETIME_FORMAT_SAVE)
				]);
			}
		}
		
	}
	
?>