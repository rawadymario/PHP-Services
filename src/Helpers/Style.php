<?php
	namespace RawadyMario\Helpers;

	class Style {
		protected static $filesArray = [];
		protected static $stylesArray = [];

		public static function AddFile(string $key, string $file): void {
			self::$filesArray[$key] = $file;
		}

		public static function RemoveFile(string $key): void {
			if (isset(self::$filesArray[$key])) {
				unset(self::$filesArray[$key]);
			}
		}

		public static function GetFiles(): array {
			return self::$filesArray;
		}

		public static function ClearFiles(): void {
			self::$filesArray = [];
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

			return Helper::ImplodeArrToStr($html, "\n");
		}

	}