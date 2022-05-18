<?php
	namespace RawadyMario\Classes\Core\OnlinePayment;

	interface OnlinePaymentInterface {

		public static function GenerateOffsiteForm(string $type, int $typeId): string;
		
		public static function GenerateOnsiteForm(string $type, int $typeId): void;

		public static function AuthenticatePayment(array $data): array;
		
		public static function SaveResponseLog(array $data): bool;
		
		public static function SendEmails(array $data): array;
		
		public static function CheckResponse(array $data): array;


	}