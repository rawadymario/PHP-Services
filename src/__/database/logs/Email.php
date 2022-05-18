<?php
	namespace RawadyMario\Classes\Database\Logs;

	use RawadyMario\Classes\Core\Database;

	class EmailLog extends Database {
	
	
		public function __construct($id=0) {
			parent::__construct();

			$this->_database	= DB_LOGS;
			$this->_table		= "email";
			$this->_key			= "id";

			$this->getInstance();

			$this->hideDeleted();
			
			$this->autoSaveCreate	= false;
			$this->autoSaveUpdate	= false;

			if ($id > 0) {
				parent::load($id);
			}
		}
		
	}
	
?>