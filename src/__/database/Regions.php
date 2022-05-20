<?php
	namespace RawadyMario\Classes\Database;
	
	use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Helpers\Helper;

	class Regions extends Database {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "regions";
			$this->_key		= "id";

			$this->autoOrder("e.`name` ASC");
			$this->getInstance();
			
			$this->autoSaveCreate	= false;
			$this->autoSaveUpdate	= false;

			if ($id > 0) {
				parent::load($id);
			}
		}
		
		public function loadByName($name="", $countryId=0) {
			$condition = "e.`name` = '$name'";
			if ($countryId > 0) {
				$condition .= " AND e.`country_id` = $countryId";
			}
			parent::listAll($condition);
		}
        

        public function loadFrom($countryId=0, $regionName="") {
            // $condition = "e.`country_id` = $countryId";
            // if ($regionName != "") {
            //     $condition .= " AND e.`name` = '$regionName'";
            // }

            // parent::listAll($condition);
        }


		public static function GetSelectArr($countryId=0, $showAll=true) {
			$arr = [];
			
			if (!$showAll && $countryId <= 0) {
				$countryId = 9999999999;
			}
            
            $condition = "1";
            if ($countryId > 0) {
                $condition = "e.`country_id` = $countryId";
            }

            $c = new self();
			$c->listAll($condition);

			foreach ($c->data AS $row) {
				$arr[$row["id"]] = $row["name"];
			}

			return $arr;
		}


		public static function RenderList($countryId=0, $slc="", $lang="en") {
			$html = "";

            $arr = self::GetSelectArr($countryId);

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

		public static function getIdFromName($name="", $countryId=0) {
			$obj = new self();
			$obj->listAll("e.`country_id` = $countryId AND e.`name` = '$name'");

			return Helper::ConvertToInt($obj->row["id"]);
		}
		
	}

?>