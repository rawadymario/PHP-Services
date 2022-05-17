<?php
	namespace RawadyMario\Helpers;

	class Style {
		protected static $files_array = [];
		protected static $stylesArray = [];

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

		public static function AddStyle(string $key, string $style): void {
			self::$stylesArray[$key] = $style;
		}

		public static function RemoveStyle(string $key): void {
			if (isset(self::$stylesArray[$key])) {
				unset(self::$stylesArray[$key]);
			}
		}

		public static function GetStyles(): array {
			return self::$stylesArray;
		}

		public static function ClearStyles(): void {
			self::$stylesArray = [];
		}

		public static function GetFilesIncludes(): string {
			$html = [];

			$files = self::GetFiles();
			foreach ($files AS $file) {
				$html[] = "<link rel=\"stylesheet\" href=\"$file\">";
			}

			$styles = self::GetStyles();
			foreach ($styles AS $style) {
				$html[] = $style;
			}

			return Helper::implode_arr_to_str($html, "\n");
		}

	}