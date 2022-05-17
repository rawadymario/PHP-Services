<?php
	namespace RawadyMario\Helpers;

	class Script {
		protected static $files_array = [];
		protected static $scriptsArray = [];

		public static function AddFile(string $key, string $file): void {
			self::$files_array[$key] = $file;
		}

		public static function RemoveFile(string $key): void {
			if (isset(self::$files_array[$key])) {
				unset(self::$files_array[$key]);
			}
		}

		public static function GetFiles(): array {
			return self::$files_array;
		}

		public static function ClearFiles(): void {
			self::$files_array = [];
		}

		public static function AddScript(string $key, string $script): void {
			self::$scriptsArray[$key] = $script;
		}

		public static function RemoveScript(string $key): void {
			if (isset(self::$scriptsArray[$key])) {
				unset(self::$scriptsArray[$key]);
			}
		}

		public static function GetScripts(): array {
			return self::$scriptsArray;
		}

		public static function ClearScripts(): void {
			self::$scriptsArray = [];
		}

		public static function GetFilesIncludes(): string {
			$html = [];

			$files = self::GetFiles();
			foreach ($files AS $file) {
				$html[] = "<script src=\"$file\"></script>";
			}

			$scripts = self::GetScripts();
			foreach ($scripts AS $script) {
				$html[] = $script;
			}

			return Helper::implode_arr_to_str($html, "\n");
		}

	}