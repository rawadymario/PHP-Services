<?php
	namespace RawadyMario\Helpers;

	use Exception;

	class ViewHelper {
		private static $ROOT_TEMPLATES_FOLDER;


		/**
		 * Set the $ROOT_TEMPLATES_FOLDER variable
		 */
		public static function SetVariableRootTemplateFolder(string $var): void {
			self::$ROOT_TEMPLATES_FOLDER = $var;
		}


		/**
		 * Get a Template
		 */
		public static function GetTemplateContent(string $pre, string $filePath, array $replace=[]): string {
			try {
				$filePath = $pre . $filePath . ".html";
				
				$body = file_get_contents($filePath);
				// $body = str_replace(array("\r\n","\r","\n"), "", $body);
				
				foreach ($replace AS $k => $v) {
					$body = str_replace($k, $v, $body);
				}
				
				return $body;
			}
			catch (Exception $e) {
				throw new Exception("Exception caught when Getting Template Content: " .  $e->getMessage() . "\n");
			}
		}


		/**
		 * Get an Email Template
		 */
		public static function GetEmailTemplate(string $filePath, array $replace=[]): string {
			try {
				return self::GetTemplateContent(self::$ROOT_TEMPLATES_FOLDER . "email/", $filePath, $replace);
			}
			catch (Exception $e) {
				throw new Exception("Exception caught when Getting Email Template Content: " .  $e->getMessage() . "\n");
			}
		}


		/**
		 * Get a Notification Template
		 */
		public static function GetNotificationTemplate(string $filePath, array $replace=[]): string {
			try {
				return self::GetTemplateContent(self::$ROOT_TEMPLATES_FOLDER . "notification/", $filePath, $replace);
			}
			catch (Exception $e) {
				throw new Exception("Exception caught when Getting Notification Template Content: " .  $e->getMessage() . "\n");
			}
		}


		/**
		 * Get a Web Notification Template
		 */
		public static function GetWebNotificationTemplate(string $filePath, array $replace=[]): string {
			try {
				return self::GetTemplateContent(self::$ROOT_TEMPLATES_FOLDER . "web-notification/", $filePath, $replace);
			}
			catch (Exception $e) {
				throw new Exception("Exception caught when Getting Web Notification Template Content: " .  $e->getMessage() . "\n");
			}
		}


		/**
		 * Get a Others Template
		 */
		public static function GetOtherTemplate(string $filePath, array $replace=[]): string {
			try {
				return self::GetTemplateContent(self::$ROOT_TEMPLATES_FOLDER . "others/", $filePath, $replace);
			}
			catch (Exception $e) {
				throw new Exception("Exception caught when Getting Others Template Content: " .  $e->getMessage() . "\n");
			}
		}

		
	}