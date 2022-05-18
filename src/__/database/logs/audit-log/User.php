<?php
	namespace RawadyMario\Classes\Database\Logs\AuditLog;

	use RawadyMario\Classes\Database\Logs\AuditLog;

	class User_AuditLog implements User_AuditLogInterface {
		const Type = AuditLog::TypeUser;

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

		public static function Verify(int $recordId, array $payload=[]) : void {
			AuditLog::Add(
				self::Type,
				AuditLog::ActionVerify,
				$recordId,
				$payload
			);
		}

		public static function ChangePassword(int $recordId, array $payload=[]) : void {
			AuditLog::Add(
				self::Type,
				AuditLog::ActionChangePass,
				$recordId,
				$payload
			);
		}

		public static function SendEmail(int $recordId, array $payload=[]) : void {
			AuditLog::Add(
				self::Type,
				AuditLog::ActionSendEmail,
				$recordId,
				$payload
			);
		}

	}