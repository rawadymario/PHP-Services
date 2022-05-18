<?php
	namespace RawadyMario\Classes\Database\Views;

	use RawadyMario\Classes\Database\OrderStatusHistory;


	class VOrderStatusHistory extends OrderStatusHistory {

		public function __construct($id=0) {
			parent::__construct();

			$this->_table	= "v_order_status_history";
			$this->_key		= "id";
			
			$this->clearDeleted();
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}
		}

	}

?>