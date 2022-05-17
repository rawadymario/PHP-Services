<?php
	namespace RawadyMario\Helpers;

	class Style {
		protected static $files_array = [];
		protected static $styles_array = [];

		public static function add_file(string $key, string $file): void {
			self::$files_array[$key] = $file;
		}

		public static function remove_file(string $key): void {
			if (isset(self::$files_array[$key])) {
				unset(self::$files_array[$key]);
			}
		}

		public static function get_files(): array {
			return self::$files_array;
		}

		public static function clear_files(): void {
			self::$files_array = [];
		}

		public static function add_style(string $key, string $style): void {
			self::$styles_array[$key] = $style;
		}

		public static function remove_style(string $key): void {
			if (isset(self::$styles_array[$key])) {
				unset(self::$styles_array[$key]);
			}
		}

		public static function get_styles(): array {
			return self::$styles_array;
		}

		public static function clear_styles(): void {
			self::$styles_array = [];
		}

		public static function get_files_includes(): string {
			$html = [];

			$files = self::get_files();
			foreach ($files AS $file) {
				$html[] = "<link rel=\"stylesheet\" href=\"$file\">";
			}

			$styles = self::get_styles();
			foreach ($styles AS $style) {
				$html[] = $style;
			}

			return Helper::implode_arr_to_str($html, "\n");
		}

	}