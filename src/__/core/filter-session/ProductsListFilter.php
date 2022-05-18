<?php
	namespace RawadyMario\Classes\Core\FilterSession;

	use RawadyMario\Classes\Core\FilterSession;

	class ProductsListFilter {

		public static function GetFilter(array $filterArr=[]) : array {
			$archived = $filterArr["archived"]	?? 0;
			$type = $archived ? FilterSession::ArchivedProductsList : FilterSession::ProductsList;

			$filterSession = new FilterSession($type);

			$userId			= $filterArr["user_id"]		?? 0;
			$active			= $filterArr["active"]		?? $filterSession->Get("active", "-1");
			$inStock		= $filterArr["in_stock"]	?? $filterSession->Get("in_stock", "-1");
			$productCatArr	= $filterArr["product_cat"]	?? $filterSession->Get("product_cat", [], true);
			
			if ($active != "-1") {
				$filterSession->Add("active", $active);
			}
			if ($inStock != "-1") {
				$filterSession->Add("in_stock", $inStock);
			}
			if (count($productCatArr) > 0) {
				$filterSession->Add("product_cat", json_encode($productCatArr));
			}

			return [
				"user_id" => $userId,
				"active" => $active,
				"archived" => $archived,
				"in_stock" => $inStock,
				"product_cat" => $productCatArr,
			];
		}

		public static function Clear(bool $archived = false) {
			$type = $archived ? FilterSession::ArchivedProductsList : FilterSession::ProductsList;
			FilterSession::Clear($type);
		}

	}