<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;

    class ProductLocation extends Database {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "product_location";
			$this->_key		= "id";

			$this->autoOrder("e.`order` ASC");

			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				$this->load($id);
			}

			$this->intArr	= [
				"active"
			];
		}
		
	}