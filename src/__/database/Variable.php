<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;

	class Variable extends Database {

		public function __construct($id=0) {
			parent::__construct();

			$this->_table	= "variable";
			$this->_key		= "id";
			
			$this->autoOrder("`order` ASC");
			$this->hideDeleted();
			$this->getInstance();

			$this->autoSaveCreate	= false;
			$this->autoSaveUpdate	= false;

			if ($id > 0) {
				parent::load($id);
			}
		}


		public function listByCategory($categoryId=0, $function="_set") {
			$condition	= "e.`category_id` = $categoryId";

			parent::listAll($condition, "", "", $function);
		}


		public static function GetSelectArr($categoryId=0, $variableId=0) {
			$arr = [];

			$c = new self();
			$c->orderBy("e.`order` ASC");
			$c->listByCategory($categoryId);

			foreach ($c->data AS $row) {
				if (
					$variableId == 0
					||
					($variableId != 0 && $row["id"] == $variableId)
				) {
					$arr[$row["id"]] = LANG == "ar" && $row["name_ar"] != "" ? $row["name_ar"] : $row["name"];
				}
			}

			return $arr;
		}

		
	}

?>