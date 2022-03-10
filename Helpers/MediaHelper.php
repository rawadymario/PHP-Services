<?php
	namespace RawadyMario\Helpers;

	class MediaHelper {
		private static $MEDIA_FOLDER = "mediafiles/";
		private static $UPLOAD_DIR;
		private static $MEDIA_ROOT;
		private static $WEBSITE_VERSION;


		/**
		 * Set the $MEDIA_FOLDER valiable
		 */
		public static function SetVariableMediaFolder(string $var): void {
			self::$MEDIA_FOLDER = $var;
		}


		/**
		 * Set the $UPLOAD_DIR valiable
		 */
		public static function SetVariableUploadDir(string $var): void {
			self::$UPLOAD_DIR = $var;
		}


		/**
		 * Set the $MEDIA_ROOT valiable
		 */
		public static function SetVariableMediaRoot(string $var): void {
			self::$MEDIA_ROOT = $var;
		}


		/**
		 * Set the $WEBSITE_VERSION valiable
		 */
		public static function SetVariableWebsiteVersion(string $var): void {
			self::$WEBSITE_VERSION = $var;
		}


		/**
		 * Adds the root folder to a url, and converts it to a safe, user friendly URL
		 * @param string $path
		 * @return string
		 */
		public static function GetMediaFullPath(string $path, string $subFldr="", bool $addDomain=false, bool $getNextGen=false, bool $withVersion=true, string $default=""): string {
			$url = "";

			if (!Helper::StringNullOrEmpty($path)) {
				$path = str_replace(self::$MEDIA_FOLDER, "", $path);

				$baseName	= pathinfo($path, PATHINFO_BASENAME);
				$fileName	= pathinfo($path, PATHINFO_FILENAME);
				$extension	= pathinfo($path, PATHINFO_EXTENSION);
				$extNextGen	= "webp";

				$pre	= str_replace($baseName, "", $path);

				$options	= [];
				if ($subFldr != "") {
					$subFldrs = [];
					if ($subFldr == "th") {
						$subFldrs = ["th", "ld", "hd"];
					}
					else if ($subFldr == "ld") {
						$subFldrs = ["ld", "hd"];
					}
					else if ($subFldr == "hd") {
						$subFldrs = ["hd"];
					}
					else {
						$subFldrs = [$subFldr];
					}

					foreach ($subFldrs AS $subFldr) {
						if ($getNextGen) {
							$options[] = $pre . $fileName . "-" . $subFldr . "." . $extNextGen;
						}
						$options[] = $pre . $fileName . "-" . $subFldr . "." . $extension;
					}
				}

				if ($getNextGen) {
					$options[] = $pre . $fileName . "." . $extNextGen;
				}
				$options[] = $pre . $fileName . "." . $extension;

				foreach ($options AS $option) {
					if (file_exists(self::$UPLOAD_DIR . $option)) {
						$url = self::$MEDIA_ROOT . $option;
						break;
					}
				}

				// if ($addDomain) {
				// 	$url = makeUrlWithWebsiteRoot($url);
				// }
			}
			else if ($default !== "") {
				$path = $default;
			}

			if ($url === "" && $path !== "") {
				$url = self::$MEDIA_ROOT . $path;
			}

			if ($url !== "" && $withVersion) {
				$url .= "?v=" . self::$WEBSITE_VERSION;
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