<?php
	namespace RawadyMario\Classes\Database\Logs\AuditLog;

	interface AuditLogInterface {		
		public static function Create(int $recordId, array $payload=[]) : void;
		public static function Edit(int $recordId, array $payload=[]) : void;
		public static function Delete(int $recordId, array $payload=[]) : void;
		public static function Activate(int $recordId, array $payload=[]) : void;
	}

	interface AuditLogImageInterface {		
		public static function UploadImage(int $recordId, array $payload=[]) : void;
		public static function DeleteImage(int $recordId, array $payload=[]) : void;
	}

	interface User_AuditLogInterface extends AuditLogInterface {
		public static function Verify(int $recordId, array $payload=[]) : void;
		public static function ChangePassword(int $recordId, array $payload=[]) : void;
		public static function SendEmail(int $recordId, array $payload=[]) : void;
	}

	interface Product_AuditLogInterface extends AuditLogInterface {
		public static function Archive(int $recordId, array $payload=[]) : void;
		public static function UnarchiveAndActivate(int $recordId, array $payload=[]) : void;
	}

	interface ProductOption_AuditLogInterface extends AuditLogInterface {}

	interface ProductCategory_AuditLogInterface extends AuditLogInterface {}