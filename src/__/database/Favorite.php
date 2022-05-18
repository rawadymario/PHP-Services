<?php
	namespace RawadyMario\Classes\Database;
	
	use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Helpers\Helper;

	class Favorite extends Database {
		const TypeProduct = 1;
		const TypeStore = 2;

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "favorite";
			$this->_key		= "id";
			
			$this->autoSaveCreate	= true;
			$this->autoSaveUpdate	= false;

			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}
		}


		public function loadByUserIdAndRecTypeId($userId=0, $recType=0, $recId=0, $params=[]) {
			$condition	= "1";
			$join		= "INNER JOIN `user` u ON (u.`id` = e.`user_id`)";
			$select		= ", u.`first_name`, u.`last_name`, u.`profile_pic`";
			$function	= "_set";
			$fields		= "e.*";

			if ($userId != 0) {
				$condition .= " AND e.`user_id` = $userId";
			}
			if ($recType != 0) {
				$condition .= " AND e.`rec_type` = $recType";
			}
			if ($recId != 0) {
				$condition .= " AND e.`rec_id` = $recId";
			}

			if (isset($params["condition"])) {
				$condition .= $params["condition"];
			}
			if (isset($params["join"])) {
				$join .= $params["join"];
			}
			if (isset($params["select"])) {
				$select .= $params["select"];
			}
			if (isset($params["function"])) {
				$function = $params["function"];
			}
			if (isset($params["fields"])) {
				$fields = $params["fields"];
			}
			
			parent::listAll($condition, $join, $select, $function, $fields);
		}


		public static function AddOrRemoveItem(array $arr) : array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$userId = Helper::ConvertToInt($arr["user_id"] ?? LOGGED_ID);
			$recType = Helper::ConvertToInt($arr["rec_type"] ?? self::TypeProduct);
			$recId = Helper::ConvertToInt($arr["rec_id"] ?? 0);
			$onlyAdd = Helper::ConvertToBool($arr["only_add"] ?? false);

			if (!IS_LOGGED) {
				$retArr["code"]	= HTTP_UNAUTHORIZED;
				$retArr["msg"]	= _text("YouMustLoginFirst");
			}
			else if ($userId > 0 && $recType > 0 && $recId > 0) {
				$favorite = new self();
				$favorite->loadByUserIdAndRecTypeId($userId, $recType, $recId);

				if (!in_array($recType, [
					self::TypeProduct,
				])) {
					return $retArr;
				}

				if ($favorite->count == 0) {
					$favorite->row["user_id"] = $userId;
					$favorite->row["rec_type"] = $recType;
					$favorite->row["rec_id"] = $recId;

					$favorite->insert();
					if (!$favorite->error) {
						$successMsg = "ActionSuccessfullyPerformed";
						switch ($recType) {
							case self::TypeProduct:
								$successMsg = "ProductAddedToFavList";
								break;
			
							case self::TypeStore:
								$successMsg = "StoreAddedToFavList";
								break;
						}
	
						$retArr["code"]	= HTTP_OK;
						$retArr["msg"]	= $successMsg;
						$retArr["response"]["newState"] = 1;
					}
				}
				else {
					if ($onlyAdd) {
						$successMsg = "ActionSuccessfullyPerformed";
						switch ($recType) {
							case self::TypeProduct:
								$successMsg = "ProductAddedToFavList";
								break;
			
							case self::TypeStore:
								$successMsg = "StoreAddedToFavList";
								break;
						}
	
						$retArr["code"]	= HTTP_OK;
						$retArr["msg"]	= $successMsg;
						$retArr["response"]["newState"] = 1;
					}
					else {
						$favorite->delete();
						if (!$favorite->error) {
							$successMsg = "ActionSuccessfullyPerformed";
							switch ($recType) {
								case self::TypeProduct:
									$successMsg = "ProductRemovedFromFavList";
									break;
				
								case self::TypeStore:
									$successMsg = "StoreRemovedFromFavList";
									break;
							}

							$retArr["code"]	= HTTP_OK;
							$retArr["msg"]	= $successMsg;
							$retArr["response"]["newState"] = 0;
						}
					}
				}
			}

			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"] ?? HTTP_INTERNALERROR);
			$retArr["msg"] = Helper::CleanHtmlText($retArr["msg"] ?? "UnknownErrorOccurred");

			return $retArr;
		}


		public static function UserExtended(int $userId) : array {
			$params = [
				"condition"	=> "",
				"join"		=> "INNER JOIN `v_product` `p` ON (`p`.`id` = `e`.`rec_id` AND `p`.`deleted` = 0)",
				"select"	=> ", `e`.`id` AS `fav_id`, `p`.*"
			];
		
			$favorite = new self();
			$favorite->groupBy("`e`.`rec_type`, `e`.`rec_id`");
			$favorite->orderBy("`e`.`user_id` ASC, `e`.`rec_type` ASC, `e`.`created_on` DESC");
			$favorite->loadByUserIdAndRecTypeId($userId, self::TypeProduct, 0, $params);

			return $favorite->data;
		}
		
	}

?>