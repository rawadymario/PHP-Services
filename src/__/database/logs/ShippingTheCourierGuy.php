<?php
	namespace RawadyMario\Classes\Database\Logs;

	use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Core\MachineInfo;
	use RawadyMario\Classes\Helpers\DateHelper;

	class ShippingTheCourierGuy extends Database {
		public const ERROR = ERROR;
		public const SUCCESS = SUCCESS;

		public function __construct($id=0) {
			parent::__construct();

			$this->_database	= DB_LOGS;
			$this->_table		= "shipping_the_courier_guy";
			$this->_key			= "id";

			$this->getInstance();

			$this->clearAutoOrder();
			$this->clearDeleted();
			
			$this->autoSaveCreate	= false;
			$this->autoSaveUpdate	= false;

			if ($id > 0) {
				parent::load($id);
			}
		}


		public function loadByTypeAndTypeId(string $type, int $typeId) {
			$condition = "`e`.`type` = '$type' AND `e`.`type_id` = $typeId";
			$this->listAll($condition);
		}
		

		public static function saveBeforeSend(string $type, int $typeId, string $submitLink, $postVals): int {
			$postValsStr = $postVals;
			if (is_array($postVals)) {
				$postValsStr = json_encode($postVals);
			}

			$s = new self();
			$s->row["type"] = $type;
			$s->row["type_id"] = $typeId;
			$s->row["submit_link"] = $submitLink;
			$s->row["post_vals"] = $postValsStr;
			$s->row["status"] = self::ERROR;
			$s->row["machine_info"] = json_encode(MachineInfo::GetAllInfo());
			$s->row["created_on"]	= date(DateHelper::DATETIME_FORMAT_SAVE);
			
			return $s->insert();
		}
		

		public static function saveFinal(string $type, int $typeId, string $submitLink, $postVals, $response, $status): int {
			$postValsStr = $postVals;
			if (is_array($postVals)) {
				$postValsStr = json_encode($postVals);
			}

			$s = new self();
			$s->row["type"] = $type;
			$s->row["type_id"] = $typeId;
			$s->row["submit_link"] = $submitLink;
			$s->row["post_vals"] = $postValsStr;
			$s->row["response"] = $response;
			$s->row["status"] = $status;
			$s->row["machine_info"] = json_encode(MachineInfo::GetAllInfo());
			$s->row["created_on"]	= date(DateHelper::DATETIME_FORMAT_SAVE);
			
			return $s->insert();
		}
		

		public static function updateResponse(string $type, int $typeId, string $status, $response): bool {
			$responseStr = $response;
			if (is_array($response)) {
				$responseStr = json_encode($response);
			}

			$s = new self();
			$s->loadByTypeAndTypeId($type, $typeId);

			if ($s->count > 0) {
				$s->row["response"] = $responseStr;
				$s->row["status"] = $status;
				$s->row["updated_on"] = date(DateHelper::DATETIME_FORMAT_SAVE);
				$s->update();

				return true;
			}
			
			return false;
		}
		
	}
	
?>