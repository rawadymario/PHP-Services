<?php
	namespace RawadyMario\Classes\Database;
	
	use RawadyMario\Classes\Core\Database;

	class CartGroup extends Database {
		
		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "cart_group";
			$this->_key		= "id";

			$this->_deleteIsAFlag = false;
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}

		}


		public function loadByUserId(int $userId) {
			$condition = "`e`.`user_id` = $userId";
			$this->listAll($condition);
		}

	}

?>