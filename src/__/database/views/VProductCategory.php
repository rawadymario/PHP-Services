<?php
	namespace RawadyMario\Classes\Database\Views;

	use RawadyMario\Classes\Core\Database;
    use RawadyMario\Classes\Helpers\Helper;

    class VProductCategory extends Database {

        public function __construct($id=0) {
            parent::__construct();
            
            $this->_table	= "v_product_category";
            $this->_key		= "id";

            $this->autoOrder("e.`order` ASC");

            $this->hideDeleted();
            $this->showActive();
            $this->getInstance();

            if ($id > 0) {
                $this->load($id);
            }
        }

        public function _setByParentId($row=[]) {
			$categoryId = Helper::ConvertToInt($row["id"]);
			$parentId = Helper::ConvertToInt($row["parent_id"]);

			if (!isset($this->list[$parentId])) {
				$this->list[$parentId] = [];
			}
			$this->list[$parentId][$categoryId] = $row;

			return parent::_set($row);
		}

        public function listAllNice($parentCategoryId=0, $params=[]) {
			$showCount			= $params["showCount"]			?? false;
			$onlyWithProducts	= $params["onlyWithProducts"]	?? false;

			$condition = "1" . ($params["condition"] ?? "");

			$allCats = new self();
			$allCats->clearActive();
			$allCats->orderBy("e.`category` ASC");
			$allCats->listAll($condition, "", "", "_setByParentId");
			
			$condition	= "e.`parent_id` = $parentCategoryId" . ($params["condition"] ?? "");
			parent::listAll($condition);
            
			$this->list = [];
			foreach ($this->data AS $row) {
				$catId = $row["id"];

				$subsArr = $allCats->list[$catId] ?? [];
				$productsCount	= Helper::ConvertToInt($row["products_count"] ?? 0);
				$subsData		= [];
				$thisCount		= $productsCount;
				foreach ($subsArr AS $k => $subRow) {
					$thisCount	= Helper::ConvertToInt($subRow["products_count"] ?? 0);

					if (!$onlyWithProducts || ($onlyWithProducts && $thisCount > 0)) {
						$productsCount += $thisCount;

						$subRow["parent_category"]	= $row["category"];
						$subRow["count"]			= $thisCount;

						if ($showCount) {
							$subRow["category"] .= " ($thisCount)";
						}

						$subsData[$k] = $subRow;
					}
				}

				if (!$onlyWithProducts || ($onlyWithProducts && (count($subsData) > 0 || $thisCount > 0))) {
					$row["parent_category"]	= "";
					$row["products_count"]	= $productsCount;
					$row["subs"]			= $subsData;
					$row["count"]			= $thisCount;

					if ($showCount) {
						$row["category"] .= " ($productsCount)";
					}

					$this->list[$catId] = $row;
					$this->count += count($subsArr);
				}
			}
		}

    }

?>