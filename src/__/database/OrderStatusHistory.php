<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;

	class OrderStatusHistory extends Database {

		public function __construct($id=0) {
			parent::__construct();

			$this->_table	= "order_status_history";
			$this->_key		= "id";
			
			$this->clearDeleted();
			$this->getInstance();

			$this->autoSaveUpdate = false;

			if ($id > 0) {
				parent::load($id);
			}
		}


		public static function GetLatest(
			int $orderId
		): array {
			$obj = new self();
			$obj->orderBy("`e`.`created_on` DESC");
			$obj->limit(0, 1);
			$obj->listAll("`e`.`order_id` = $orderId");

			return $obj->row;
		}

	}

?>