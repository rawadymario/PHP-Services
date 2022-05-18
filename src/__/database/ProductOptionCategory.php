<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;

	class ProductOptionCategory extends Database {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "product_option_category";
			$this->_key		= "id";

			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}
		}

		public function _setByUserId($row=[]) {
			$uId	= $row["user_id"];

			if (!isset($this->list[$uId])) {
				$this->list[$uId] = [];
			}
			$this->list[$uId][] = $row;

			return parent::_set($row);
		}

		public static function addDefault($userId=0, $storeId=0) {
			$arr = [
				[
					"title" => "Colour"
				],
				[
					"title" => "Size"
				],
				[
					"title" => "Brand"
				],
			];

			foreach ($arr AS $row) {
				$oc = new ProductOptionCategory();
				$oc->row["user_id"]		= $userId;
				$oc->row["store_id"]	= $storeId;
				$oc->fillRow($row);
				$oc->insert();
			}
		}

		public static function GetSelectArr($userId=0, $storeId=0) {
			$conditionArr	= [
				"e.`user_id` = $userId"
			];
			if ($storeId > 0) {
				$conditionArr[] = "e.`store_id` = $storeId";
			}

			$condition	= "(" . implode(" OR ", $conditionArr) . ")";

			$c = new self();
			$c->orderBy("`title` ASC");
			$c->listAll($condition);
			
			$arr = [
				"options"	=> [],
				"params"	=> [],
			];
			foreach ($c->data AS $row) {
				$arr["options"][$row["id"]]	= $row["title"];
				$arr["params"][$row["id"]]	= [
					"data-title" => $row["title"]
				];
			}

			return $arr;
		}
		
	}