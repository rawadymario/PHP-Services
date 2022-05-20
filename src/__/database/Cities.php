<?php
	namespace RawadyMario\Classes\Database;
	
	use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Helpers\Helper;

	class Cities extends Database {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "cities";
			$this->_key		= "id";

			$this->autoOrder("e.`name` ASC");
			$this->getInstance();
			
			$this->autoSaveCreate	= false;
			$this->autoSaveUpdate	= false;

			if ($id > 0) {
				parent::load($id);
			}
		}
		

		public function _setByName($row=[]) {
			$this->list[$row["name"]] = $row;

			return parent::_set($row);
		}
		

		public function loadByName($name="", $regionId=0, $countryId=0, $forShipping=false) {
			$condition = "e.`name` = '$name'";
			if ($regionId > 0) {
				$condition .= " AND e.`region_id` = $regionId";
			}
			if ($countryId > 0) {
				$condition .= " AND e.`country_id` = $countryId";
			}
			if ($forShipping) {
				$condition .= " AND (e.`is_dhl` = 1)";
			}
			parent::listAll($condition);
		}
		
		
		public function loadByNameLike($name="", $regionId=0, $countryId=0, $forShipping=false) {
			$condition = "e.`name` LIKE '%$name%'";
			if ($regionId > 0) {
				$condition .= " AND e.`region_id` = $regionId";
			}
			if ($countryId > 0) {
				$condition .= " AND e.`country_id` = $countryId";
			}
			if ($forShipping) {
				$condition .= " AND (e.`is_dhl` = 1)";
			}
			parent::listAll($condition);
		}


		public function loadFrom($countryId=0, $regionId=0, $regionName="", $params=[]) {
            $condition  = "1";
            $join       = "";
            $select     = "";

            if ($countryId > 0) {
                $condition .= " AND e.`country_id` = $countryId";
            }
            if ($regionId > 0) {
                $condition .= " AND e.`region_id` = $regionId";
            }
            if ($regionName != "") {
                $join   = "INNER JOIN `regions` r ON (r.`id` = e.`region_id` AND r.`name` = '$regionName')";
                $select = ", r.`name` AS `region_name`, r.`code` AS `region_code`";
			}
			
			if (isset($params["condition"])) {
				$condition .= $params["condition"];
			}

            parent::listAll($condition, $join, $select);
        }


		public static function GetSelectArr($countryId=0, $regionId=0, $regionName="", $showAll=true, $forShipping=false) {
			$arr	= [];
			$params	= [];
			
			if (!$showAll && $countryId <= 0) {
				$countryId = 9999999999;
			}
			if (!$showAll && $regionId <= 0) {
				$regionId = 9999999999;
			}
			if ($forShipping) {
				$params["condition"] = " AND (e.`is_dhl` = 1)";
			}

            $c = new self();
			$c->loadFrom($countryId, $regionId, $regionName, $params);

			foreach ($c->data AS $row) {
				$arr[$row["id"]] = $row["name"];
			}

			return $arr;
		}


		public static function RenderList($countryId=0, $regionId=0, $regionName="", $slc="", $lang="en", $forShipping=false) {
			$html = "";

            $arr = self::GetSelectArr($countryId, $regionId, $regionName, true, $forShipping);

			foreach ($arr AS $k => $v) {
				$_slc	= $slc == $k ? " selected='selected'" : "";

				$html .= "<option value='$k'$_slc>$v</option>";
			}

			return $html;
		}


		public static function getNameFromId($id=0) {
			$obj = new self($id);

			return $obj->row["name"];
		}

		public static function getIdFromName($name="", $countryId=0, $regionId=0) {
			$obj = new self();
			$obj->listAll("e.`country_id` = $countryId AND e.`region_id` = $regionId AND e.`name` = '$name'");

			return Helper::ConvertToInt($obj->row["id"]);
		}


		public static function checkName($name="", $countryId=0) {
			$obj = new self();
			$obj->loadByName($name, 0, $countryId);

			return $obj->count > 0;
		}
		
	}

?>