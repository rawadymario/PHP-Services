<?php
	namespace RawadyMario\Helpers;

	class UploadHelper {


		/**
		 * Create the given file/folder
		 */
		public static function CreateFileOrFolder(string $dir, string $permission="0777") : void {
			if (!file_exists($dir) && !is_dir($dir)) {
				mkdir($dir, $permission, true);
			}
		}


		/**
		 * Delete the given file/folder
		 */
		public static function DeleteFileOrFolder(string $dir) : void {
			if (file_exists($dir)) {
				if (is_dir($dir)) {
					rmdir($dir);
				}

				if (!is_dir($dir)) {
					unlink($dir);
				}
			}
		}


	}