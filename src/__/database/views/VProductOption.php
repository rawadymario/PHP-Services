<?php
	namespace RawadyMario\Classes\Database\Views;

	use RawadyMario\Classes\Core\Database;

	class VProductOption extends Database {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "v_product_option";
			$this->_key		= "id";

			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}
		}

		public function _setByProductId($row=[]) {
			$productId = $row["product_id"];

			if (!isset($this->list[$productId])) {
				$this->list[$productId] = [];
			}
			$this->list[$productId][] = $row;

			return parent::_set($row);
		}

		public function _groupByCategoryId($row=[]) {
			$categoryId			= $row["category_id"];
			$categoryTitle		= $row["option_category_title"];
			
			if (!isset($this->list[$categoryId])) {
				$this->list[$categoryId] = [
					"info"	=> [
						"id"		=> $categoryId,
						"title"		=> $categoryTitle,
					],
					"options"	=> []
				];
			}
			$this->list[$categoryId]["options"][] = $row;

			return parent::_set($row);
		}

		public function forWebsite(bool $flag=true) {
			if ($flag) {
				$this->showActive();
			}
			else {
				$this->clearActive();
			}
		}


		public function loadByProduct($productId=0, $params=[]) {
			$condition	= isset($params["condition"])	? $params["condition"]	: "1";
			$join		= isset($params["join"])		? $params["join"]		: "";
			$select		= isset($params["select"])		? $params["select"]		: "";
			$function	= isset($params["function"])	? $params["function"]	: "_set";
			$fields		= isset($params["fields"])		? $params["fields"]		: "e.*";
			
			if ($condition == "") {
				$condition = "1";
			}
			$condition .= " AND e.`product_id` = $productId";

			parent::listAll($condition, $join, $select, $function, $fields);
		}
		
	}

?>