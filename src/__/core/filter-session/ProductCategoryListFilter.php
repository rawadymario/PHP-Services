<?php
	namespace RawadyMario\Classes\Core\FilterSession;

	use RawadyMario\Classes\Core\FilterSession;

	class ProductCategoryListFilter {

		public static function GetFilter(array $filterArr=[]) : array {
			$filterSession = new FilterSession(FilterSession::ProductCategoryList);

			$userId			= $filterArr["user_id"]		?? 0;
			$used			= $filterArr["used"]		?? $filterSession->Get("used", "-1");
			$active			= $filterArr["active"]		?? $filterSession->Get("active", "-1");
			
			if ($used != "-1") {
				$filterSession->Add("used", $used);
			}
			if ($active != "-1") {
				$filterSession->Add("active", $active);
			}

			return [
				"user_id" => $userId,
				"used" => $used,
				"active" => $active,
			];
		}

		public static function Clear() {
			FilterSession::Clear(FilterSession::ProductCategoryList);
		}

	}