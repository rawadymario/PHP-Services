<?php
	namespace RawadyMario\Classes\Database\Logs\AuditLog;

	use RawadyMario\Classes\Database\Logs\AuditLog;

	class Product_AuditLog implements Product_AuditLogInterface, AuditLogImageInterface {
		const Type = AuditLog::TypeProduct;

		public static function Create(int $recordId, array $payload=[]) : void {
			AuditLog::Add(
				self::Type,
				AuditLog::ActionCreate,
				$recordId,
				$payload
			);
		}

		public static function Edit(int $recordId, array $payload=[]) : void {
			AuditLog::Add(
				self::Type,
				AuditLog::ActionEdit,
				$recordId,
				$payload
			);
		}

		public static function Delete(int $recordId, array $payload=[]) : void {
			AuditLog::Add(
				self::Type,
				AuditLog::ActionDelete,
				$recordId,
				$payload
			);
		}

		public static function Activate(int $recordId, array $payload=[]) : void {
			AuditLog::Add(
				self::Type,
				AuditLog::ActionActivate,
				$recordId,
				$payload
			);
		}

		public static function Archive(int $recordId, array $payload=[]) : void {
			AuditLog::Add(
				self::Type,
				AuditLog::ActionArchive,
				$recordId,
				$payload
			);
		}

		public static function UnarchiveAndActivate(int $recordId, array $payload=[]) : void {
			AuditLog::Add(
				self::Type,
				AuditLog::ActionArchive,
				$recordId,
				$payload
			);
		}

		public static function UploadImage(int $recordId, array $payload=[]) : void {
			AuditLog::Add(
				self::Type,
				AuditLog::ActionUploadImage,
				$recordId,
				$payload
			);
		}

		public static function DeleteImage(int $recordId, array $payload=[]) : void {
			AuditLog::Add(
				self::Type,
				AuditLog::ActionDeleteImage,
				$recordId,
				$payload
			);
		}

	}