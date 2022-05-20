<?php
	namespace RawadyMario\Classes\Database\Logs\AuditLog;

	use RawadyMario\Classes\Database\Logs\AuditLog;

	class Banner_AuditLog implements AuditLogInterface {
		const Type = AuditLog::TypeBanner;

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

	}