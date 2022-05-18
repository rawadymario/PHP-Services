<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;
	
	class OrderProduct extends Database {

		public function __construct($id=0) {
			parent::__construct();

			$this->_table	= "order_product";
			$this->_key		= "id";
			
			$this->clearDeleted();
			$this->clearAutoOrder();
			$this->getInstance();

			$this->autoSaveCreate	= false;
			$this->autoSaveUpdate	= false;

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


		public function listAllExtended($condition="1", $join="", $select="", $function="_set", $fields="e.*") {
			$join	.= "INNER JOIN `product` `p` ON (`p`.`id` = `e`.`product_id`)";
			$select .= ", `p`.`name` AS `product_name`, `p`.`meta_url`";

			parent::listAll($condition, $join, $select, $function, $fields);
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

			$this->listAllExtended($condition, $join, $select, $function, $fields);
		}

	}

?>