<?php
	namespace RawadyMario\Helpers;

use RawadyMario\Exceptions\FileNotFoundException;
use RawadyMario\Exceptions\NotEmptyParamException;

	class MediaHelper {
		private static $MEDIA_FOLDER = "mediafiles/";
		private static $UPLOAD_DIR;
		private static $MEDIA_ROOT;
		private static $WEBSITE_VERSION;


		/**
		 * Set the $MEDIA_FOLDER valiable
		 */
		public static function SetVariableMediaFolder(
			string $var
		): void {
			self::$MEDIA_FOLDER = $var;
		}


		/**
		 * Set the $UPLOAD_DIR valiable
		 */
		public static function SetVariableUploadDir(
			string $var
		): void {
			if (!Helper::StringEndsWith($var, ["/", "\\"])) {
				$var .= "/";
			}
			self::$UPLOAD_DIR = $var;
		}


		/**
		 * Set the $MEDIA_ROOT valiable
		 */
		public static function SetVariableMediaRoot(
			string $var
		): void {
			if (!Helper::StringEndsWith($var, ["/", "\\"])) {
				$var .= "/";
			}
			self::$MEDIA_ROOT = $var;
		}


		/**
		 * Set the $WEBSITE_VERSION valiable
		 */
		public static function SetVariableWebsiteVersion(
			string $var
		): void {
			self::$WEBSITE_VERSION = $var;
		}


		/**
		 * Adds the root folder to a url, and converts it to a safe, user friendly URL
		 * @param string $path
		 * @return string
		 */
		public static function GetMediaFullPath(
			?string $path=null,
			?string $subFolder=null,
			bool $getNextGen=false,
			bool $withVersion=true,
			bool $withDomain=true
		): string {
			if (Helper::StringNullOrEmpty(self::$UPLOAD_DIR)) {
				throw new NotEmptyParamException("UPLOAD_DIR");
			}
			if (Helper::StringNullOrEmpty(self::$MEDIA_ROOT)) {
				throw new NotEmptyParamException("MEDIA_ROOT");
			}
			if (Helper::StringNullOrEmpty($path)) {
				throw new NotEmptyParamException("path");
			}

			$path = str_replace(self::$MEDIA_FOLDER, "", $path);
			[
				"dirname" => $dirName,
				"basename" => $baseName,
				"filename" => $fileName,
				"extension" => $extension,
			] = pathinfo($path);
			if ($getNextGen) {
				$newExtension = "webp";
				$path = str_replace(".{$extension}", ".{$newExtension}", $path);
				$extension = $newExtension;
			}

			if (!Helper::StringNullOrEmpty($subFolder)) {
				$subFolders = [];
				if ($subFolder == "th") {
					$subFolders = ["th", "ld", "hd"];
				}
				else if ($subFolder == "ld") {
					$subFolders = ["ld", "hd"];
				}
				else if ($subFolder == "hd") {
					$subFolders = ["hd"];
				}
				else {
					$subFolders = [$subFolder];
				}

				$options = [];
				foreach ($subFolders AS $subFolder) {
					$options[] = $dirName . "/" . $fileName . "-" . $subFolder . "." . $extension;
				}
			}
			$options[] = $dirName . "/" . $fileName . "." . $extension;

			$url = "";
			foreach ($options AS $option) {
				if (file_exists(self::$UPLOAD_DIR . $option)) {
					$url = self::$MEDIA_ROOT . $option;
					break;
				}
			}

			if (Helper::StringNullOrEmpty($url)) {
				throw new FileNotFoundException(self::$MEDIA_ROOT . $path);
			}

			if ($withVersion && !Helper::StringNullOrEmpty(self::$WEBSITE_VERSION)) {
				$url .= "?v=" . self::$WEBSITE_VERSION;
			}

			if (!$withDomain) {
				$url = str_replace(self::$MEDIA_ROOT, "", $url);
			}

			return $url;
		}


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