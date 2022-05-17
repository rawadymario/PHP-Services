<?php
	namespace RawadyMario\Helpers;

	class Script {
		protected static $files_array = [];
		protected static $scripts_array = [];

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

		public static function add_script(string $key, string $script): void {
			self::$scripts_array[$key] = $script;
		}

		public static function remove_script(string $key): void {
			if (isset(self::$scripts_array[$key])) {
				unset(self::$scripts_array[$key]);
			}
		}

		public static function get_scripts(): array {
			return self::$scripts_array;
		}

		public static function clear_scripts(): void {
			self::$scripts_array = [];
		}

		public static function get_files_includes(): string {
			$html = [];

			$files = self::get_files();
			foreach ($files AS $file) {
				$html[] = "<script src=\"$file\"></script>";
			}

			$scripts = self::get_scripts();
			foreach ($scripts AS $script) {
				$html[] = $script;
			}

			return Helper::implode_arr_to_str($html, "\n");
		}

	}