<?php
	namespace RawadyMario\Classes\Database\Views;

	use RawadyMario\Classes\Database\Product;
	use RawadyMario\Classes\Helpers\Helper;
	use RawadyMario\Classes\Helpers\QueryHelper;

	class VProduct extends Product {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "v_product";
			$this->_key		= "id";

			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				$this->clearDeleted();
				parent::load($id);
			}
		}


		public function randomizeOrder($offset=0) {
			shuffle($this->data);

			if ($offset > 0) {
				$arr = [];

				for ($i = 0; $i < $offset; $i++) {
					if (isset($this->data[$i])) {
						$arr[] = $this->data[$i];
					}
				}
				$this->data = $arr;
			}
		}


		public static function GetListFromFilter(array $params=[], int $offset=0, int $nbOfRecs=0) : array {
			$key = $params["key"] ?? "";
			$type = $params["type"] ?? "";
			$fromPrice = $params["from_price"] ?? "";
			$toPrice = $params["to_price"] ?? "";
			$categories = $params["categories"] ?? [];
			$orderBy = $params["order_by"] ?? PRODUCT_ORDER_DEFAULT;
			$productIds = $params["product_ids"] ?? "";

			$condition = "1";
			if (!Helper::StringNullOrEmpty($key)) {
				$condition .= self::GetNameFilter($key);
			}
			if (!Helper::StringNullOrEmpty($type)) {
				switch ($type) {
					case "onsale":
						$condition .= " AND (`e`.`discount_percentage` > 0 OR `e`.`discount_amount` > 0)";
						break;
					
					case "new":
						$condition .= " AND `e`.`is_new` = 1";
						break;
				}
			}
			if (!Helper::StringNullOrEmpty($fromPrice)) {
				$condition .= " AND `e`.`min_sell_price_after_discount` >= $fromPrice";
			}
			if (!Helper::StringNullOrEmpty($toPrice)) {
				$condition .= " AND `e`.`max_sell_price_after_discount` <= $toPrice";
			}
			if (!Helper::ArrayNullOrEmpty($categories)) {
				$subCondition = [];
				foreach ($categories AS $catId) {
					$subCondition[] = "FIND_IN_SET($catId, e.`category`)";

					// $cats = new ProductCategory();
					// $cats->listAll("e.`parent_id` = $catId");
					// if ($cats->count > 0) {
					// 	foreach ($cats->data AS $cRow) {
					// 		$subCondition[] = "FIND_IN_SET(" .$cRow["id"]  . ", e.`category`)";
					// 	}
					// }
				}

				if (count($subCondition) > 0) {
					$condition .= " AND (" . implode(" OR ", $subCondition) . ")";
				}
			}
			if (!Helper::StringNullOrEmpty($productIds)) {
				$condition .= " AND `e`.`id` NOT IN ($productIds)";
			}

			$orderParam = "";
			switch ($orderBy) {
				case PRODUCT_ORDER_DEFAULT:
					$mainOffset = $offset;
					$mainNbOfRecs = $nbOfRecs;

					$offset = 0;
					$nbOfRecs = PRODUCTS_TO_RANDOMIZE;
					$orderParam = "RAND()";
					break;
					
				case PRODUCT_ORDER_NEWEST:
					$orderParam = "`e`.`created_on` DESC";
					break;
					
				case PRODUCT_ORDER_PRICE_ASC:
					$orderParam = "`e`.`min_sell_price_after_discount` ASC";
					break;
					
				case PRODUCT_ORDER_PRICE_DESC:
					$orderParam = "`e`.`min_sell_price_after_discount` DESC";
					break;
					
			}

			$self = new self();
			$self->showActive();
			$self->hideArchived();
			$self->limit($offset, $nbOfRecs);
			if (!Helper::StringNullOrEmpty($orderParam)) {
				$self->orderBy($orderParam);
			}
			$self->listAll($condition);

			if ($orderBy === PRODUCT_ORDER_DEFAULT) {
				$self->randomizeOrder($mainNbOfRecs);
			}

			$prodIds = Helper::ImplodeArrStr(array_map(function($r) {
				return $r["id"];
			}, $self->data), ",");

			return [
				"data" => $self->data,
				"countAll" => $self->countAll,
				"productIds" => $prodIds,
			];
		}


		public static function GetImages(string $imagesStr) {
			$images = [];
			if (!Helper::StringNullOrEmpty($imagesStr)) {
				$images = explode(",", $imagesStr);
			}

			while (count($images) < 1) {
				$images[] = PRODUCT_DEFAULT_IMAGE;
			}

			return $images;
		}


		private static function GetNameFilter(string $key="", bool $extended=true, bool $withCat=true, bool $addAnd=true) : string {
			$condition = "";

			if ($extended) {
				$_cond = "
					   e.`name` = '$key'
					OR e.`name` LIKE '%$key%'
					OR " . QueryHelper::GenericNameFilter($key, "e.`name`");

				if ($withCat) {
					$_cond .= "
						OR FIND_IN_SET('$key', `e`.`categories_names`)
						OR `e`.`categories_names` LIKE '%$key%'
						OR " . QueryHelper::GenericNameFilter($key, "`e`.`categories_names`");
				}
				
				$condition .= "($_cond)";
			}
			else {
				$_cond = "e.`name` = '$key'";

				if ($withCat) {
					$_cond = " OR FIND_IN_SET('$key', `e`.`categories_names`)";
				}

				$condition .= "($_cond)";
			}

			if ($addAnd && !Helper::StringNullOrEmpty($condition)) {
				$condition = " AND " . $condition;
			}

			return $condition;
		}
		
	}