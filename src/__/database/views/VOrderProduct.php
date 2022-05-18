<?php
	namespace RawadyMario\Classes\Database\Views;

	use RawadyMario\Classes\Database\OrderProduct;

	class VOrderProduct extends OrderProduct {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "v_order_product";
			$this->_key		= "id";

			$this->clearDeleted();
			$this->clearAutoOrder();
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
            }
            
		}

		public function _setByOrderId($row=[]) {
			$orderId = $row["order_id"];

			if (!isset($this->list[$orderId])) {
				$this->list[$orderId] = [];
			}
			$this->list[$orderId][] = $row;

			return parent::_set($row);
		}

		public function loadByOrder($orderId=0, $params=[]) {
			$condition	= isset($params["condition"])	? $params["condition"]	: "1";
			$join		= isset($params["join"])		? $params["join"]		: "";
			$select		= isset($params["select"])		? $params["select"]		: "";
			$function	= isset($params["function"])	? $params["function"]	: "_set";
			$fields		= isset($params["fields"])		? $params["fields"]		: "e.*";

			if ($condition == "") {
				$condition = "1";
			}
			$condition .= " AND e.`order_id` = $orderId";

			$this->listAll($condition, $join, $select, $function, $fields);
		}
        
    }

?>