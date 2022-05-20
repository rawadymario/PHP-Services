<?php
	namespace RawadyMario\Classes\Core\FilterSession;

	use RawadyMario\Classes\Core\FilterSession;

	class UsersListFilter {

		public static function GetFilter(array $filterArr=[]) : array {
			$filterSession = new FilterSession(FilterSession::UsersList);

			$userId		= $filterArr["user_id"]		?? 0;
			$type		= $filterArr["type"]		?? $filterSession->Get("type", 0);
			$verified	= $filterArr["verified"]	?? $filterSession->Get("verified", "-1");
			$active		= $filterArr["active"]		?? $filterSession->Get("active", "-1");

			if ($type > 0) {
				$filterSession->Add("type", $type);
			}
			if ($verified != "-1") {
				$filterSession->Add("verified", $verified);
			}
			if ($active != "-1") {
				$filterSession->Add("active", $active);
			}

			return [
				"user_id" => $userId,
				"type" => $type,
				"verified" => $verified,
				"active" => $active,
			];
		}

		public static function Clear() {
			FilterSession::Clear(FilterSession::UsersList);
		}

	}