<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Database\Views\VProductCategory;
	use RawadyMario\Classes\Helpers\Helper;

    class ProductCategory extends Database {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "product_category";
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

		public function _setByParentId($row=[]) {
			$parentId = Helper::ConvertToInt($row["parent_id"]);

			if (!isset($this->list[$parentId])) {
				$this->list[$parentId] = [];
			}
			$this->list[$parentId][] = $row;

			return parent::_set($row);
		}

		public function load($id=0, $force_reload=true) {
			parent::clearActive();
			parent::load($id);
		}


		public function loadByIdAndName($id=0, $category="") {
			$condition	= "e.`id` != $id AND LOWER(e.`category`) = '" . strtolower($category) . "'";

			parent::listAll($condition);
		}


		public static function GetSelectArr($onlyParents=-1) {
			$condition	= "1";

			if ($onlyParents != -1) {
				if ($onlyParents == 1) {
					$condition .= " AND e.`parent_id` = 0";
				}
				else {
					$condition .= " AND e.`parent_id` != 0";
				}
			}

			$c = new self();
			$c->listAll($condition);
			
			$arr = [];
			foreach ($c->data AS $row) {
				$arr[$row["id"]] = LANG == "ar" && $row["category_ar"] != "" ? $row["category_ar"] : $row["category"];
			}

			return $arr;
		}

		public static function GetUsedCategories(int $onlyParents=-1, int $archived=0) : array {
			$pCondition	= "e.`category` IS NOT NULL AND e.`archived` = $archived";
			
			$c = new Product();
			$c->listAll($pCondition);
			
			$categoryArr = [];
			foreach ($c->data as $row) {
				$categoryArr[] = $row["category"];
			}
			
			$cCondition = "e.`id` IN (" . implode(",", $categoryArr) . ")";
			if ($onlyParents != -1) {
				if ($onlyParents == 1) {
					$cCondition .= " AND e.`parent_id` = 0";
				}
				else {
					$cCondition .= " AND e.`parent_id` != 0";
				}
			}
			$c = new self();
			$c->listAll($cCondition);

			$arr = [];
			foreach ($c->data AS $row) {
				$arr[$row["id"]] = $row["category"];
			}

			return $arr;
		}


		public static function GetSelectArrMultiLevel($params=[], bool $toUpper=true) {
			$c = new VProductCategory();
			$c->listAllNice(0, $params);
			
			$arr = [];
			foreach ($c->list AS $row) {
				if ($toUpper) {
					$row["category"] = strtoupper($row["category"]);
				}

				$subs	= [];
				foreach ($row["subs"] AS $subRow) {
					if ($toUpper) {
						$subRow["category"] = strtoupper($subRow["category"]);
					}

					$subs[$subRow["id"]] = $subRow["category"];
				}
				
				if (count($subs) > 0) {
					$arr[$row["id"]]	= [
						"label"	=> $row["category"],
						"opts"	=> $subs
					];
				}
				else {
					$arr[$row["id"]] = $row["category"];
				}
			}

			return $arr;
		}


		public static function GetSelectArrMultiLevelExtended($params=[]) {
			$c = new VProductCategory();
			$c->listAllNice(0, $params);
			
			$arr = [];
			foreach ($c->list AS $row) {
				$subs	= [];
				foreach ($row["subs"] AS $subRow) {
					$subs[$subRow["id"]] = $subRow;
				}
				
				if (count($subs) > 0) {
					$arr[$row["id"]] = array_merge($row, ["opts" => $subs]);
				}
				else {
					$arr[$row["id"]] = $row;
				}
			}

			return $arr;
		}


		public static function SetPickedSession($id=0) {
			$_SESSION[SESSION_NAME]["PickedProductCategoryId"] = $id;
		}


		public static function GetPickedSession() {
			return $_SESSION[SESSION_NAME]["PickedProductCategoryId"] ?? 0;
		}


		public static function CanSave(array $category=[]) : bool {
			return true;
		}


		public static function CanActivate(array $category=[]) : bool {
			$categoryId	= $category["id"] ?? 0;
			$active		= $category["active"] ?? 0;
			$productsNb	= $category["products_count"] ?? 0;

			if ($categoryId == 0) {
				return false;
			}

			if ($productsNb == 0) {
				return true;
			}
			if ($active == 0) {
				return true;
			}
			
			return false;
		}
		
		public static function CanDelete(array $category=[]) : bool {
			$categoryId	= $category["id"] ?? 0;
			$active		= $category["active"] ?? 0;
			$productsNb	= $category["products_count"] ?? 0;
			
			return $categoryId > 0 && $productsNb == 0;
		}
		
	}