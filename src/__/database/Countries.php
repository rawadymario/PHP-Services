<?php
	namespace RawadyMario\Classes\Database;
	
	use RawadyMario\Classes\Core\Database;

	class Countries extends Database {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "countries";
			$this->_key		= "id";

			$this->hideDeleted();
			$this->autoOrder("e.`order` ASC");
			$this->getInstance();
			
			$this->autoSaveCreate	= false;
			$this->autoSaveUpdate	= false;

			if ($id > 0) {
				parent::load($id);
			}
		}


		public function loadByCountryCode($code="") {
			parent::loadBy($code, "iso");
		}


		public static function GetSelectArr($forShipping=false, array $allowedIds=[]) {
			$arr = [];

			$condition	= "1";
			if ($forShipping) {
				$condition .= " AND (e.`is_dhl` = 1)";
			}
			if (count($allowedIds) > 0) {
				$condition .= " AND `e`.`id` IN (" . implode(", ", $allowedIds) . ")";
			}

			$c = new self();
			$c->listAll($condition);

			foreach ($c->data AS $row) {
				$arr[$row["id"]] = $row["name"];
			}

			return $arr;
		}


		public static function GetCodeArr(int $style=1, array $allowedIds=[]) {
			$arr = [];

			$condition = "1";
			if (count($allowedIds) > 0) {
				$condition .= " AND `e`.`id` IN (" . implode(", ", $allowedIds) . ")";
			}

			$c = new self();
			$c->listAll($condition);

			foreach ($c->data AS $row) {
				switch ($style) {
					case 2:
						$title = "+" . $row["dial"] . " - " . $row["name"];
						break;

					case 1:
					default:
						$title = $row["name"] . " (+" . $row["dial"] . ")";
						break;
				}
				$arr["+" . $row["dial"]] = $title;
			}

			return $arr;
		}


		public static function RenderList($slc="", $lang="en") {
			$html = "";

			$c = new self();
			$c->listAll();

			foreach ($c->data AS $row) {
				$_key	= $row["id"];
				$_val	= $lang == "ar" ? $row["name"] : $row["name"];

				$_slc	= $slc == $_key ? " selected='selected'" : "";

				$html .= "<option value='$_key'$_slc>$_val</option>";
			}

			return $html;
		}
		
	}

?>