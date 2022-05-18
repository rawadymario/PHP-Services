<?php
	namespace RawadyMario\Classes\Database\Logs;

	use RawadyMario\Classes\Core\Database;

	class AuditLog extends Database {
		const TypeUser = "User";
		const TypeProduct = "Product";
		const TypeProductCategory = "ProductCategory";
		const TypeProductOption = "ProductOption";
		const TypeStore = "Store";
		const TypeBanner = "Banner";
		
		const ActionCreate = "Create";
		const ActionEdit = "Edit";
		const ActionDelete = "Delete";
		const ActionActivate = "Activate";
		const ActionVerify = "Verify";
		const ActionArchive = "Archive";
		const ActionChangePass = "ChangePassword";
		const ActionSendEmail = "SendEmail";
		const ActionUploadImage = "UploadImage";
		const ActionDeleteImage = "DeleteImage";
		const AddToFavorites = "AddToFavorites";
		const RemoveFromFavorites = "RemoveFromFavorites";
		
		const ActionEmailType_ResendVerifyEmail = "ResendVerifyEmail";
		
		public function __construct(int $id=0) {
			parent::__construct();

			$this->_database	= DB_LOGS;
			$this->_table		= "audit_log";
			$this->_key			= "id";
			
			parent::getInstance();

			$this->autoSaveUpdate = false;

			if ($id > 0) {
				parent::load($id);
			}
		}

		public static function Add(
			string $type,
			string $action,
			int $recordId,
			array $payload=[]
		) : void {
			if (defined("LOGGED_ID") && LOGGED_ID > 0 && (!isset($payload["logged_id"]) || $payload["logged_id"] == 0)) {
				$payload["logged_id"] = LOGGED_ID;
			}
			
			$payloadStr = "";
			if (count($payload) > 0) {
				$payloadStr = json_encode($payload);
			}
			
			$auditLog = new self();
			$auditLog->insert([
				"type" => $type,
				"action" => $action,
				"record_id" => $recordId,
				"payload" => $payloadStr
			]);
		}
		
	}
	
?>