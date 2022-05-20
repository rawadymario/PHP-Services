<?php
	namespace RawadyMario\Classes\Database\Views;

	use RawadyMario\Classes\Database\Order;

	class VOrder extends Order {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "v_order";
			$this->_key		= "id";

			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				$this->clearDeleted();
				parent::load($id);
			}
		}
		
		public function listOrder($orderId=0, $userId=0, $storeId=0, $params=[]) {
			$condition	= "1";
			$join		= "";
			$select		= "";
			$function	= "_set";
			$fields		= "e.*";

			if ($orderId > 0) {
				$condition	.= " AND e.`id` = " . $orderId;
			}
			if ($userId > 0) {
				$condition	.= " AND e.`user_id` = " . $userId;
			}
			if ($storeId > 0) {
				$condition	.= " AND e.`store_id` = " . $storeId;
			}
			
			if (isset($params["condition"])) {
				$condition .= $params["condition"];
			}
			if (isset($params["join"])) {
				$join .= $params["join"];
			}
			if (isset($params["select"])) {
				$select .= $params["select"];
			}
			if (isset($params["function"])) {
				$function = $params["function"];
			}
			if (isset($params["fields"])) {
				$fields = $params["fields"];
			}

			$this->listAll($condition, $join, $select, $function, $fields);
		   
			$orderProducts	= new VOrderProduct();
			$orderProducts->listAll("1", "", "", "_setByOrderId");
			foreach ($this->data AS $k => $row) {
				$orderId = $row["id"];

				$this->data[$k]["products"] = isset($orderProducts->list[$orderId]) ? $orderProducts->list[$orderId] : [];
			}
			$this->row["products"] = isset($orderProducts->list[$orderId]) ? $orderProducts->list[$orderId] : [];
		}
		
	}

?>