<?php
	namespace RawadyMario\Classes\Database;
	
	use RawadyMario\Classes\Core\Database;

	class IpInfo extends Database {
		
		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "ip_info";
			$this->_key		= "ip_address";
			
			$this->autoSaveCreate = false;
			$this->autoSaveUpdate = false;

			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}
		}
	}

?>