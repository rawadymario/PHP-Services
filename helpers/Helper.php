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
			int $decimalPlaces=2,
			bool $numberFormat=false
		): float {
			if ((isset($val)) && (trim($val) !== "")) {
				$x = floatval($val);
				$x = round($x, $decimalPlaces);

				if ($numberFormat) {
					$x = number_format($x, $decimalPlaces);
				}

				if (is_numeric($x) && is_nan($x)) {
					return 0;
				}

				return $x;
			}

			return 0;
		}


		/**
		 * Check if the given string is null or empty
		 */
		public static function StringNullOrEmpty($str) : bool {
			return is_null($str) || empty($str) || (is_string($str) && (strlen($str) == 0 || $str == ""));
		}


		/**
		 * Check if the given array is null or empty
		 */
		public static function ArrayNullOrEmpty(?array $arr) : bool {
			return is_null($arr) || !is_array($arr) || count($arr) == 0;
		}


		/**
		 * Check if the given object is null or empty
		 */
		public static function ObjectNullOrEmpty(?object $obj) : bool {
			return is_null($obj) || !is_object($obj) || count(array($obj)) == 0;
		}


		/**
		 * Encrypt a Password
		 */
		public static function EncryptPassword($password) {
			return hash("sha512", trim($password));
		}


		/**
		 * Generate a Random String
		 */
		public static function GenerateRandomKey($length=8, $hasInt=true, $hasString=false, $hasSymbols=false, $lang="en") {
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

			/* Add random characters to $key until $length is reached */
			for ($i = 0; $i < $length; $i++) {
				$minRandNb	= 0;
				$maxRandNb	= strlen($possible)-1;
				$rand		= mt_rand($minRandNb, $maxRandNb);

				/* Pick a random character from the possible ones */
				$char = substr($possible, $rand, 1);

				$key .= $char;
			}

			return $key;
		}


		/**
		 * Removes all the slashes from a string
		 */
		public static function RemoveSlashes(string $data): string {
			return stripslashes(trim(implode("", explode("\\", $data) ) ) );
		}


		/**
		 * Removes all the spaces from a string
		 */
		public static function RemoveSpaces(string $data): string {
			return str_replace(" ", "", trim($data));
		}


		/**
		 * Limit a text to a fixed number of characters
		 */
		public static function TruncateStr(string $text, int $nbOfChar, string $extension="...", string $lang="en"): string {
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
		public static function StringBeginsWith(string $string, string $search): int {
			return (strncmp($string, $search, strlen($search)) == 0);
		}


		/**
		 * Search if is a string end with a special characters combination
		 */
		public static function StringEndsWith(string $string, string $search) {
			return substr($string, (strlen($string) - strlen($search))) == $search ? true : false;
		}


		/**
		 * Search if is a string contacins a value
		 */
		public static function StringHasChar(string $string, string $search) {
			return strpos($string, $search) !== false;
		}


		/**
		 * Check if a given substring exists in a string
		 */
		public static function IsInString(string $search, string $string){
			return strpos($string, $search) !== false ? true : false;
		}


		/**
		 * Search for the allowed tags in the content and display them
		 */
		public static function StripHtml(string $content, string $allow=""): string {
			return strip_tags($content, $allow);
		}


		/**
		 * Replace all values in a text
		 */
		public static function TextReplace(string $text, array $params=[]): string {
			foreach ($params AS $k => $v) {
				$text = str_replace($k, $v, $text);
			}
	
			return $text;
		}


		/**
		 * Separate Camel Case String
		 */
		public static function SplitCamelcaseString(string $str, string $split=" "): string {
			$pieces = preg_split("/(?=[A-Z])/", $str);
	
			return implode($split, $pieces);
		}


		/**
		 * Get the string value safely
		 */
		public static function GetStringSafe(string $str) : string {
			if (self::StringNullOrEmpty($str)) {
				return "";
			}
			return $str;
		}


		/**
		 * Converts the given String into an Array
		 */
		public static function StringToArr(string $str, int $elemLength=0, string $separator="") : array {
			$arr = [$str];
			
			if ($elemLength > 0) {
				$arr = [];
				while (strlen($str) > $elemLength) {
					$chunk = substr($str, 0, $elemLength);
	
					$arr[] = $chunk;
					$str   = substr($str, $elemLength);
				}
				if (strlen($str) > 0) {
					$arr[] = $str;
				}
			}
			else if ($separator != "") {
				$arr = explode($separator, $str);
			}
			
			return $arr;
		}


		/**
		 * Generates a Class Name from the Given String
		 */
		public static function GenerateClassNameFromString(string $str) : string {
			return str_replace(" ", "", ucwords(str_replace("-", " ", $str)));
		}


		/**
		 * Returns a safe file name
		 */
		public static function SafeFileName(string $str): string {
			return preg_replace('[^\p{L}0-9\-_]', "-", strtolower($str));
		}


		/**
		 * Returns a safe file name by
		 *  - Converting spaces to '-',
		 *  - Removing chars that are not alphanumeric,
		 *  - Combine multiple dashes (i.e., '---') into one dash '-'.
		 */
		public static function SafeFileName2(string $str): string {
			$str = preg_replace("/[-]+/", "-", preg_replace("/[^a-z0-9-]/", "", strtolower( str_replace(" ", "-", $str) ) ) );
			return $str;
		}


		/**
		 * Converts the given string into a safe one | Supports English & Arabic
		 */
		public static function SafeUrl(string $str, string $trimChar="-"): string {
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
		public static function HasArabicChar(string $str): bool {
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
				/* pos = ord($char); we cant use that, its not binary safe */
				$pos = self::uniord($char);
				/* echo $char . " --> " . $pos . PHP_EOL; */

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
		 * Converts a string into an array
		 */
		public static function ExplodeStrArr(string $str, string $delimiter=","): array {
			if ($str != "") {
				$pos = strpos($str, $delimiter);
				if (!$pos) {
					return [$str];
				}
				else {
					return explode($delimiter, $str);
				}
			}
			else {
				return [];
			}
		}


		/**
		 * Returns a [delimiter] seperated string from the values inside the given array
		 */
		public static function ImplodeArrStr(array $array, string $delimiter=" "): string {
			$string = "";

			foreach ($array as $str) {
				if ($string != "" && $str != "") {
					$string .= $delimiter;
				}

				$string .=  $str;
			}

			return $string;
		}


		/**
		 * Get the value of the given key in a given array
		 */
		public static function GetValueFromArr(string $key, array $arr): string {
			$str = "";

			if (isset($arr) && is_array($arr) && sizeof($arr) > 0 && isset($arr[$key])) {
				$str = $arr[$key];
			}

			return $str;
		}


		/**
		 * Unset Empty Values from the given object/array
		 */
		public static function UnsetEmptyValues(array $var): array {
			foreach ($var AS $k => $v) {
				if
				(
					(is_string($v) && $v == "")
					||
					(is_array($v) && sizeof($v) == 0)
					||
					is_null($v)
				) {
					if (is_object($var)) {
						unset($var->$k);
					}
					else if (is_array($var)) {
						unset($var[$k]);
					}
				}
			}
	
			return $var;
		}


		/**
		 * Generate Key Value String from Array
		 */
		public static function GererateKeyValueStringFromArray(array $params=[], string $keyPrefix="", string $keyValueJoin="=", string $valueHolder="\"", string $elemsJoin="") : string {
			$str = "";
	
			foreach ($params AS $k => $v) {
				$k = $keyPrefix . $k;
				
				$str .= ($str != "" ? " " : "") . $k . $keyValueJoin . $valueHolder . $v . $valueHolder . $elemsJoin;
			}
	
			return $str;
		}


		/**
		 * Checks if the given directory is available in the domain folders
		 */
		public static function DirExists($dir_name=false, string $path="./"): bool {
			if (!$dir_name) {
				return false;
			}

			if (is_dir($path . $dir_name)) {
				return true;
			}

			$tree = glob($path . "*", GLOB_ONLYDIR);
			if ($tree && count($tree) > 0) {
				foreach ($tree AS $dir) {
					if (self::DirExists($dir_name, $dir . "/")) {
						return true;
					}
				}
			}

			return false;
		}


		/**
		 * Retreive Youtube embed id from the video full link
		 */
		public static function GetYoutubeId(string $url) {
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
			if (false !== $result) {
				return $matches[1];
			}

			return false;
		}


		/**
		 * Encrypt a Link
		 */
		public static function EncryptLink($var) : string {
			$str = "";
			if (is_string($var)) {
				$str = $var;
			}
			if (is_array($var)) {
				$str = json_encode($var);
			}

			$str = str_replace("&", "[amp;]", base64_encode($str));
	
			return $str;
		}

		
		/**
		 * Dencrypt a Link
		 */
		public static function DecryptLink(string $str="") : string {
			$str = str_replace("[amp;]", "&", $str);
			$str = base64_decode($str);
	
			return $str;
		}


		/**
		 * Get Status Class from the given code
		 */
		public static function GetStatusClassFromCode(string $code) : string {
			$class = Status::INFO;

			switch ($code) {
				case Code::SUCCESS:
				case HttpCode::OK:
				case HttpCode::CREATED:
				case HttpCode::ACCEPTED:
					$class = Status::SUCCESS;
					break;

				case Code::ERROR:
				case HttpCode::BADREQUEST:
				case HttpCode::UNAUTHORIZED:
				case HttpCode::FORBIDDEN:
				case HttpCode::NOTFOUND:
				case HttpCode::NOTALLOWED:
				case HttpCode::INTERNALERROR:
				case HttpCode::UNAVAILABLE:
					$class = Status::ERROR;
					break;

				case Code::WARNING:
					$class = Status::WARNING;
					break;

				case Code::INFO:
				case Code::COMMON_INFO:
				case HttpCode::CONTINUE:
				case HttpCode::PROCESSING:
					$class = Status::INFO;
					break;
			}

			return $class;
		}


		/**
		 * Get HTML content from the given file path
		 */
		public static function GetHtmlContentFromFile(string $filePath) : string {
			$html = "";
			if (file_exists($filePath)) {
				$html = file_get_contents($filePath);
			}
			return $html;
		}


		/**
		 * Get JSON content from the given file path
		 */
		public static function GetJsonContentFromFileAsArray(string $filePath) : array {
			$json = [];
			if (file_exists($filePath)) {
				$json = json_decode(file_get_contents($filePath), true);
			}
			return $json;
		}


		/**
		 * Function used in "HasArabicChar"
		 */
		private static function uniord($u) {
			/* I just copied this function from the php.net comments, but it should work fine! */
			$k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
			$k1 = ord(substr($k, 0, 1));
			$k2 = ord(substr($k, 1, 1));

			return $k2 * 256 + $k1;
		}

		// public static function GetFullUrl() {}
		// public static function GetPathWithVersion() {}

	}
