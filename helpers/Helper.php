<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Constants\Code;
	use RawadyMario\Constants\HttpCode;
	use RawadyMario\Constants\Status;

	class Helper {
		

		/**
		 * Returns a Clean String
		 */
		public static function CleanString(
			string $str
		): string {
			$str = trim($str);
			$str = stripslashes($str);
	
			//TODO: mysqli escape ?
			
			return $str;
		}


		/**
		 * Converts HTML tags in a string to be visible, and vice versa
		 */
		public static function CleanHtmlText(
			string $data,
			bool $convertSpecialChars=true
		): string {
			$data = self::CleanString($data);

			if ($convertSpecialChars) {
				$data = htmlspecialchars($data, ENT_QUOTES);
			}

			return $data;
		}


		/**
		 * Converts a value to a boolean
		 */
		public static function ConvertToBool(
			$val
		) : bool {
			switch (gettype($val)) {
				case "string":
					$val = trim($val);
					if (
						Helper::StringNullOrEmpty($val)
						||
						$val === "false"
					) {
						return false;
					}
					return true;
				
				case "integer":
				case "double":
					$val = Helper::ConvertToDec($val);
					return $val > 0;
					break;
			}
			
			return false;
		}


		/**
		 * Converts a value to an integer
		 */
		public static function ConvertToInt(
			$val
		): int {
			if ((isset($val)) && (trim($val) !== "")) {
				return round($val) ;
			}
			return 0;
		}


		/**
		 * Converts a value to a decimal
		 */
		public static function ConvertToDec(
			$val,
			int $decimalPlaces=2
		): float {
			if ((isset($val)) && (trim($val) !== "")) {
				$x = floatval($val);
				$x = round($x, $decimalPlaces);

				if (is_numeric($x) && is_nan($x)) {
					return 0;
				}

				return $x;
			}

			return 0;
		}


		/**
		 * Converts a value to a decimal and returns as a String
		 */
		public static function ConvertToDecAsString(
			$val,
			int $decimalPlaces=0
		): string {
			return number_format(self::ConvertToDec($val, $decimalPlaces), $decimalPlaces);
		}


		/**
		 * Check if the given string is null or empty
		 */
		public static function StringNullOrEmpty(
			$str
		): bool {
			return is_null($str) || empty($str) || (is_string($str) && (strlen($str) == 0 || $str == ""));
		}


		/**
		 * Check if the given array is null or empty
		 */
		public static function ArrayNullOrEmpty(
			?array $arr
		): bool {
			return is_null($arr) || !is_array($arr) || count($arr) == 0;
		}


		/**
		 * Check if the given object is null or empty
		 */
		public static function ObjectNullOrEmpty(
			?object $obj
		): bool {
			return is_null($obj) || !is_object($obj) || count(array($obj)) == 0;
		}


		/**
		 * Encrypt a Password
		 */
		public static function EncryptPassword(
			string $password
		): string {
			return hash("sha512", trim($password));
		}


		/**
		 * Generate a Random String
		 */
		public static function GenerateRandomKey(
			int $length=8,
			bool $hasInt=true,
			bool $hasString=false,
			bool $hasSymbols=false,
			string $lang="en"
		): string {
			$key = "";
			$possible = "";

			if ($hasInt) {
				if ($lang == "en" || $lang == "all") {
					$possible .= "0123456789";
				}
				if ($lang == "ar" || $lang == "all") {
					$possible .= "٠١٢٣٤٥٦٧٨٩";
				}
			}

			if ($hasString) {
				if ($lang == "en" || $lang == "all") {
					$possible .= "abcdefghijklmnopqrstuvwxyz";
					$possible .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
				}
				if ($lang == "ar" || $lang == "all") {
					// $possible .= "ابتثجحخدذرزسشصضطظعغفقكلمنهوي";
					$possible .= "ضصثقفغعهخحجدشسيبلاتنمكطئءؤرلاىةوزظ";
				}
			}

			if ($hasSymbols) {
				$possible .= "!@#$%^&*()_-+=?\/|`~.,<>";
			}

			if ($possible !== "") {
				/* Add random characters to $key until $length is reached */
				for ($i = 0; $i < $length; $i++) {
					$minRandNb	= 0;
					$maxRandNb	= strlen($possible)-1;
					$rand		= mt_rand($minRandNb, $maxRandNb);
	
					/* Pick a random character from the possible ones */
					$char = substr($possible, $rand, 1);
	
					$key .= $char;
				}
			}

			return $key;
		}


		/**
		 * Removes all the slashes from a string
		 */
		public static function RemoveSlashes(
			string $str
		): string {
			return stripslashes(trim(implode("", explode("\\", $str))));
		}


		/**
		 * Removes all the spaces from a string
		 */
		public static function RemoveSpaces(
			string $str
		): string {
			return str_replace(" ", "", trim($str));
		}


		/**
		 * Limit a text to a fixed number of characters
		 */
		public static function TruncateStr(
			string $text,
			int $nbOfChar,
			string $extension="...",
			string $lang="en"
		): string {
			if ($lang == "ar") {
				$nbOfChar = $nbOfChar * 1.8;
			}

			$text = self::CleanString($text);

			if (strlen($text) > $nbOfChar) {
				$text = substr($text, 0, $nbOfChar) . $extension;
			}

			return $text;
		}


		/**
		 * Search if is a string begins with a special characters combination
		 */
		public static function StringBeginsWith(
			string $string,
			string $search
		): bool {
			return (strncmp($string, $search, strlen($search)) == 0);
		}


		/**
		 * Search if is a string end with a special characters combination
		 */
		public static function StringEndsWith(
			string $string,
			string $search
		): bool {
			return substr($string, (strlen($string) - strlen($search))) == $search ? true : false;
		}


		/**
		 * Search if is a string contacins a value
		 */
		public static function StringHasChar(
			string $string,
			string $search
		): bool {
			return strpos($string, $search) !== false;
		}


		/**
		 * Check if a given substring exists in a string
		 */
		public static function IsInString(
			string $search,
			string $string
		): bool {
			return strpos($string, $search) !== false ? true : false;
		}


		/**
		 * Search for the allowed tags in the content and display them
		 */
		public static function StripHtml(
			string $content,
			$allow=""
		): string {
			return strip_tags($content, $allow);
		}


		/**
		 * Replace all values in a text
		 */
		public static function TextReplace(
			string $text,
			array $params=[]
		): string {
			foreach ($params AS $k => $v) {
				$text = str_replace($k, $v, $text);
			}
	
			return $text;
		}


		/**
		 * Separate Camel Case String
		 */
		public static function SplitCamelcaseString(
			string $str,
			string $split=" "
		): string {
			$pieces = preg_split("/(?=[A-Z])/", $str);
			return trim(implode($split, $pieces));
		}


		/**
		 * Get the string value safely
		 */
		public static function GetStringSafe(
			?string $str
		): string {
			if (self::StringNullOrEmpty($str)) {
				return "";
			}
			return $str;
		}


		/**
		 * Generates a Class Name from the Given String
		 */
		public static function GenerateClassNameFromString(
			string $str
		): string {
			return str_replace(
				" ",
				"",
				ucwords(
					str_replace(
						"-",
						" ",
						$str
					)
				)
			);
		}


		/**
		 * Converts the given string into a safe one | Supports English & Arabic
		 */
		public static function SafeName(
			string $str,
			string $trimChar="-"
		): string {
			$friendlyURL = htmlentities($str, ENT_COMPAT, "UTF-8", false);
			$friendlyURL = preg_replace('/&([a-z]{1,2})(?:acute|lig|grave|ring|tilde|uml|cedil|caron);/i','\1',$friendlyURL);
			$friendlyURL = html_entity_decode($friendlyURL,ENT_COMPAT, "UTF-8");
			$friendlyURL = preg_replace ( "/[^أ-يa-zA-Z0-9٠-٩_.-]/u", $trimChar, $friendlyURL );
			$friendlyURL = preg_replace('/-+/', $trimChar, $friendlyURL);
			$friendlyURL = trim($friendlyURL, $trimChar);

			$isArabic = self::HasArabicChar($str);
			if (!$isArabic) {
				$friendlyURL = strtolower($friendlyURL);
			}

			return $friendlyURL;
		}


		/**
		 * Checks if the given str contains any arabic characters
		 */
		public static function HasArabicChar(
			string $str
		): bool {
			if(mb_detect_encoding($str) !== 'UTF-8') {
				$str = mb_convert_encoding($str, mb_detect_encoding($str), "UTF-8");
			}

			/*
			$str = str_split($str); <- this function is not mb safe, it splits by bytes, not characters. we cannot use it
			$str = preg_split('//u',$str); <- this function would probably work fine but there was a bug reported in some php version so it pslits by bytes and not chars as well
			*/

			preg_match_all("/.|\n/u", $str, $matches);
			$chars = $matches[0];
			$arabic_count = 0;
			$latin_count = 0;
			$total_count = 0;

			foreach ($chars AS $char) {
				/* BEGIN: I just copied this function from the php.net comments, but it should work fine! */
				$k = mb_convert_encoding($char, 'UCS-2LE', 'UTF-8');
				$k1 = ord(substr($k, 0, 1));
				$k2 = ord(substr($k, 1, 1));

				$pos = $k2 * 256 + $k1;
				/* END: I just copied this function from the php.net comments, but it should work fine! */

				if ($pos >= 1536 && $pos <= 1791) {
					$arabic_count++;
				}
				else if ($pos > 123 && $pos < 123) {
					$latin_count++;
				}
				$total_count++;
			}

			if ($arabic_count > 0) {
				return true;
			}
			else {
				return false;
			}
		}

		
		/**
		 * Converts the given String into an Array
		 */
		public static function ExplodeStrToArr(
			?string $str,
			string $delimiter="",
			int $chunkLength=0
		): array {
			if (self::StringNullOrEmpty($str)) {
				return [];
			}

			if (!self::StringNullOrEmpty($delimiter)) {
				return explode($delimiter, $str);
			}
			
			if ($chunkLength > 0) {
				$arr = [];
				while (strlen($str) > $chunkLength) {
					$chunk = substr($str, 0, $chunkLength);
	
					$arr[] = $chunk;
					$str   = substr($str, $chunkLength);
				}
				if (strlen($str) > 0) {
					$arr[] = $str;
				}
				return $arr;
			}

			return [$str];
		}


		/**
		 * Returns a [delimiter] seperated string from the values inside the given array
		 */
		public static function ImplodeArrToStr(
			?array $array,
			string $delimiter=" "
		): string {
			if (self::ArrayNullOrEmpty($array)) {
				return "";
			}

			return implode($delimiter, self::UnsetArrayEmptyValues($array));
		}


		/**
		 * Get the value of the given key in a given array
		 */
		public static function GetValueFromArrByKey(
			?array $arr,
			string $key=""
		): string {
			if (self::ArrayNullOrEmpty($arr) || !isset($arr[$key])) {
				return "";
			}
			return $arr[$key];
		}


		/**
		 * Unset Empty Values from the given object/array
		 */
		public static function UnsetArrayEmptyValues(
			?array $array
		): array {
			if (self::ArrayNullOrEmpty($array)) {
				return [];
			}

			return array_values(
				array_filter(
					$array,
					function($value) {
						if (!Helper::StringNullOrEmpty($value)) {
							return $value;
						}
					}
				)
			);
		}


		/**
		 * Generate Key Value String from Array
		 */
		public static function GererateKeyValueStringFromArray(
			?array $params,
			string $keyPrefix="",
			string $keyValueJoin="=",
			string $valueHolder="\"",
			string $elemsJoin=" "
		): string {
			if (self::ArrayNullOrEmpty($params)) {
				return "";
			}

			$str = "";
			foreach ($params AS $k => $v) {
				$k = $keyPrefix . $k;
				$str .= ($str != "" ? $elemsJoin : "") . $k . $keyValueJoin . $valueHolder . $v . $valueHolder;
			}
			return $str;
		}


		/**
		 * Checks if the given directory is available in the domain folders
		 */
		public static function DirExists(
			?string $dirName,
			string $path="./",
			bool $checkSubFolders=false
		): bool {
			if (self::StringNullOrEmpty($dirName)) {
				return false;
			}

			if (is_dir($path . $dirName)) {
				return true;
			}

			if ($checkSubFolders) {
				$tree = glob($path . "*", GLOB_ONLYDIR);
				if ($tree && count($tree) > 0) {
					foreach ($tree AS $dir) {
						if (self::DirExists($dirName, $dir . "/")) {
							return true;
						}
					}
				}
			}

			return false;
		}


		/**
		 * Retreive Youtube embed id from the video full link
		 */
		public static function GetYoutubeId(
			?string $url
		): string {
			if (self::StringNullOrEmpty($url)) {
				return "";
			}

			$pattern =
				'%^# Match any youtube URL
				(?:https?://)?  # Optional scheme. Either http or https
				(?:www\.)?      # Optional www subdomain
				(?:             # Group host alternatives
				youtu\.be/    # Either youtu.be,
				| youtube\.com  # or youtube.com
				(?:           # Group path alternatives
				/embed/     # Either /embed/
				| /v/         # or /v/
				| .*v=        # or /watch\?v=
				)             # End path alternatives.
				)               # End host alternatives.
				([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
				($|&).*         # if additional parameters are also in query string after video id.
				$%x';

			$result = preg_match($pattern, $url, $matches);
			if ($result !== false) {
				return $matches[1];
			}

			return "";
		}


		/**
		 * Encrypt a Link
		 */
		public static function EncryptLink(
			?string $link
		): string {
			if (self::StringNullOrEmpty($link)) {
				return "";
			}
			return str_replace("&", "[amp;]", base64_encode($link));
		}

		
		/**
		 * Dencrypt a Link
		 */
		public static function DecryptLink(
			?string $link
		): string {
			if (self::StringNullOrEmpty($link)) {
				return "";
			}
			return base64_decode(str_replace("[amp;]", "&", $link));
		}


		/**
		 * Get Status Class from the given code
		 */
		public static function GetStatusClassFromCode(
			int $code
		): string {
			switch ($code) {
				case Code::SUCCESS:
				case HttpCode::OK:
				case HttpCode::CREATED:
				case HttpCode::ACCEPTED:
					return Status::SUCCESS;

				case Code::ERROR:
				case HttpCode::BADREQUEST:
				case HttpCode::UNAUTHORIZED:
				case HttpCode::FORBIDDEN:
				case HttpCode::NOTFOUND:
				case HttpCode::NOTALLOWED:
				case HttpCode::INTERNALERROR:
				case HttpCode::UNAVAILABLE:
					return Status::ERROR;

				case Code::WARNING:
					return Status::WARNING;

				case Code::INFO:
				case Code::COMMON_INFO:
				case HttpCode::CONTINUE:
				case HttpCode::PROCESSING:
					return Status::INFO;
					
				default:
					return Status::INFO;
			}
		}


		/**
		 * Get HTML content from the given file path
		 */
		public static function GetHtmlContentFromFile(
			?string $filePath
		): string {
			if (self::StringNullOrEmpty($filePath)) {
				return "";
			}
			if (!file_exists($filePath)) {
				return "";
			}
			return file_get_contents($filePath);
		}


		/**
		 * Get JSON content from the given file path
		 */
		public static function GetJsonContentFromFileAsArray(
			?string $filePath
		): array {
			if (self::StringNullOrEmpty($filePath)) {
				return [];
			}
			if (!file_exists($filePath)) {
				return [];
			}
			return json_decode(file_get_contents($filePath), true);
		}


		/**
		 * Adds the root folder to a url, and converts it to a safe, user friendly URL
		 */
		public static function GenerateFullUrl(
			string $page,
			string $lang="",
			array $safeParams=[],
			array $optionalParams=[],
			string $root="",
			bool $safeUrl=true
		) {
			$args = "";
			$finalSafeParams = [];

			if ($lang != "") {
				$finalSafeParams["lang"] = $lang;
			}

			foreach ($safeParams AS $k => $v) {
				$finalSafeParams[$k] = $v;
			}

			foreach ($finalSafeParams AS $k => $v) {
				if (!$safeUrl) {
					$args .= $args === "" ? "?" : "&";
				}
				$args .= !$safeUrl ? $k . "=" . $v : "/" . $v;
			}

			foreach ($optionalParams as $k => $v){
				if (is_array($v)) {
					foreach ($v AS $v1) {
						if ($v1 !== "") {
							$args .= (strpos($args, "?") === false ? "?" : "&") . $k . "%5B%5D=" . $v1 ;	
						}
					}
				}
				else {
					if ($v !== "") {
						$args .= (strpos($args, "?") === false ? "?" : "&") . $k . "=" . $v ;
					}
				}
			}

			if (!self::StringNullOrEmpty($root) && !self::StringEndsWith($root, "/")) {
				$root .= "/";
			}
			$url = $root . $page . $args;

			$urlScheme = "";
			if (self::StringBeginsWith($url, "http://")) {
				$urlScheme = "http://";
			}
			if (self::StringBeginsWith($url, "https://")) {
				$urlScheme = "https://";
			}

			if (!self::StringNullOrEmpty($urlScheme)) {
				$url = str_replace($urlScheme, "", $url);
			}
			
			while (strpos($url, "//") !== false) {
				$url = str_replace("//", "/", $url) ;
			}
			$url = $urlScheme . $url;

			return $url ;
		}


		/**
		 * Adds a version parameter to the given path
		 */
		public static function AddVersionParameterToPath(
			string $path,
			string $websiteRoot,
			string $version=""
		) {
			return self::GenerateFullUrl($path, "", [], [
				"v" => $version
			], $websiteRoot);
		}


		/**
		 * Get all files in a path 
		 */
		public static function GetAllFiles(
			string $path,
			bool $recursive=false
		): array {
			$filesArr = [];

			if (is_dir($path)) {
				$files = scandir($path);
		
				foreach ($files AS $file) {
					if (!is_dir($path . "/" . $file)) {
						$filesArr[] = $path . "/" . $file;
					}
					else {
						if ($recursive && $file !== "." && $file !== "..") {
							$filesArr = array_merge($filesArr, self::GetAllFiles($path . "/" . $file, $recursive));
						}
					}
				}
			}

			return $filesArr;
		}


		/**
		 * Converts a multidimentional array to a single dimentional array
		 */
		public static function ConvertMultidimentionArrayToSingleDimention(
			array $arrayToConvert,
			string $preKey=""
		): array {
			$returnArray = [];

			foreach ($arrayToConvert AS $k => $v) {
				if (is_array($v)) {
					$returnArray = array_merge($returnArray, 
						self::ConvertMultidimentionArrayToSingleDimention($v, $preKey . $k . ".")
					);
				}
				else {
					$returnArray[$preKey . $k] = $v;
				}
			}

			return $returnArray;
		}

	}
