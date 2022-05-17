<?php
	namespace RawadyMario\Media\Helpers;

	use RawadyMario\Exceptions\FileNotFoundException;
	use RawadyMario\Exceptions\NotEmptyParamException;
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Media\Models\Image;

	class Media {
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
			if (!Helper::string_ends_with($var, ["/", "\\"])) {
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
			if (!Helper::string_ends_with($var, ["/", "\\"])) {
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
			?string $imageCode=null,
			bool $getNextGen=false,
			bool $withVersion=true,
			bool $withDomain=true
		): string {
			if (Helper::string_null_or_empty(self::$UPLOAD_DIR)) {
				throw new NotEmptyParamException("UPLOAD_DIR");
			}
			if (Helper::string_null_or_empty(self::$MEDIA_ROOT)) {
				throw new NotEmptyParamException("MEDIA_ROOT");
			}
			if (Helper::string_null_or_empty($path)) {
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

			if (!Helper::string_null_or_empty($imageCode)) {
				$imageCodes = [];
				if ($imageCode === Image::THUMBNAIL_CODE) {
					$imageCodes = [
						Image::THUMBNAIL_CODE,
						Image::LOW_DEF_CODE,
						Image::HIGH_DEF_CODE
					];
				}
				else if ($imageCode === Image::LOW_DEF_CODE) {
					$imageCodes = [
						Image::LOW_DEF_CODE,
						Image::HIGH_DEF_CODE
					];
				}
				else if ($imageCode === Image::HIGH_DEF_CODE) {
					$imageCodes = [
						Image::HIGH_DEF_CODE
					];
				}
				else {
					$imageCodes = [$imageCode];
				}

				$options = [];
				foreach ($imageCodes AS $imageCode) {
					$options[] = $dirName . "/" . $fileName . "-" . $imageCode . "." . $extension;
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

			if (Helper::string_null_or_empty($url)) {
				throw new FileNotFoundException(self::$MEDIA_ROOT . $path);
			}

			if ($withVersion && !Helper::string_null_or_empty(self::$WEBSITE_VERSION)) {
				$url .= "?v=" . self::$WEBSITE_VERSION;
			}

			if (!$withDomain) {
				$url = str_replace(self::$MEDIA_ROOT, "", $url);
			}

			return $url;
		}

	}