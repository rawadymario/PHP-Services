<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Exceptions\FileNotFoundException;
	use RawadyMario\Exceptions\NotEmptyParamException;
	use RawadyMario\Models\Code;
	use RawadyMario\Models\HttpCode;
	use RawadyMario\Language\Models\Lang;
	use RawadyMario\Models\Status;

	class Helper {


		/**
		 * Returns a Clean String
		 */
		public static function clean_string(
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
		public static function clean_html_text(
			string $data,
			bool $convert_special_chars=true
		): string {
			$data = self::clean_string($data);

			if ($convert_special_chars) {
				$data = htmlspecialchars($data, ENT_QUOTES);
			}

			return $data;
		}


		/**
		 * Converts a value to a boolean
		 */
		public static function convert_to_bool(
			$val
		) : bool {
			switch (gettype($val)) {
				case "boolean":
					return $val;
				case "string":
					$val = trim($val);
					if (
						Helper::string_null_or_empty($val)
						||
						$val === "false"
					) {
						return false;
					}
					return true;

				case "integer":
				case "double":
					$val = Helper::convert_to_dec($val);
					return $val > 0;
					break;
			}

			return false;
		}


		/**
		 * Converts a value to an integer
		 */
		public static function convert_to_int(
			$val
		): int {
			if ((isset($val)) && (trim($val) !== "")) {
				if ($val < 0) {
					return intval($val);
				}
				return round($val) ;
			}
			return 0;
		}


		/**
		 * Converts a value to a decimal
		 */
		public static function convert_to_dec(
			$val,
			int $decimal_places=2
		): float {
			if ((isset($val)) && (trim($val) !== "")) {
				$val = round(floatval($val), $decimal_places);
				if (is_numeric($val) && is_nan($val)) {
					return 0;
				}
				return $val;
			}
			return 0;
		}


		/**
		 * Converts a value to a decimal and returns as a String
		 */
		public static function convert_to_dec_as_string(
			$val,
			int $decimal_places=0
		): string {
			return number_format(self::convert_to_dec($val, $decimal_places), $decimal_places);
		}


		/**
		 * Check if the given string is null or empty
		 */
		public static function string_null_or_empty(
			$str
		): bool {
			return is_null($str) || empty($str) || (is_string($str) && (strlen($str) == 0 || $str == ""));
		}


		/**
		 * Check if the given array is null or empty
		 */
		public static function array_null_or_empty(
			?array $arr
		): bool {
			return is_null($arr) || !is_array($arr) || count($arr) == 0;
		}


		/**
		 * Check if the given object is null or empty
		 */
		public static function object_null_or_empty(
			?object $obj
		): bool {
			return is_null($obj) || !is_object($obj) || count(array($obj)) == 0;
		}


		/**
		 * Encrypt a Password
		 */
		public static function encrypt_password(
			string $password
		): string {
			return hash("sha512", trim($password));
		}


		/**
		 * Generate a Random String
		 */
		public static function generate_random_key(
			int $length=8,
			bool $has_int=true,
			bool $has_string=false,
			bool $has_symbols=false,
			string $lang=Lang::EN
		): string {
			$key = "";
			$possible = "";

			if ($has_int) {
				if ($lang == Lang::EN || $lang == Lang::ALL) {
					$possible .= "0123456789";
				}
				if ($lang == Lang::AR || $lang == Lang::ALL) {
					$possible .= "٠١٢٣٤٥٦٧٨٩";
				}
			}

			if ($has_string) {
				if ($lang == Lang::EN || $lang == Lang::ALL) {
					$possible .= "abcdefghijklmnopqrstuvwxyz";
					$possible .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
				}
				if ($lang == Lang::AR || $lang == Lang::ALL) {
					// $possible .= "ابتثجحخدذرزسشصضطظعغفقكلمنهوي";
					$possible .= "ضصثقفغعهخحجدشسيبلاتنمكطئءؤرلاىةوزظ";
				}
			}

			if ($has_symbols) {
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
		public static function remove_slashes(
			string $str
		): string {
			return stripslashes(trim(implode("", explode("\\", $str))));
		}


		/**
		 * Removes all the spaces from a string
		 */
		public static function remove_spaces(
			string $str
		): string {
			return str_replace(" ", "", trim($str));
		}


		/**
		 * Limit a text to a fixed number of characters
		 */
		public static function truncate_string(
			string $text,
			int $nb_of_char,
			string $extension="...",
			string $lang=Lang::EN
		): string {
			if ($lang == Lang::AR) {
				$nb_of_char = $nb_of_char * 1.8;
			}

			$text = self::clean_string($text);

			if (strlen($text) > $nb_of_char) {
				$text = substr($text, 0, $nb_of_char) . $extension;
			}

			return $text;
		}


		/**
		 * Search if is a string begins with a special characters combination
		 */
		public static function string_begins_with(
			string $string,
			$search
		): bool {
			if (is_array($search)) {
				foreach ($search AS $s) {
					if ((strncmp($string, $s, strlen($s)) == 0)) {
						return true;
					}
				}
				return false;
			}
			else {
				return (strncmp($string, $search, strlen($search)) == 0);
			}
		}


		/**
		 * Search if is a string end with a special characters combination
		 */
		public static function string_ends_with(
			string $string,
			$search
		): bool {
			if (is_array($search)) {
				foreach ($search AS $s) {
					if (substr($string, (strlen($string) - strlen($s))) == $s) {
						return true;
					}
				}
				return false;
			}
			else {
				return substr($string, (strlen($string) - strlen($search))) == $search ? true : false;
			}
		}


		/**
		 * Search if is a string contacins a value
		 */
		public static function string_has_char(
			string $string,
			string $search
		): bool {
			return strpos($string, $search) !== false;
		}


		/**
		 * Check if a given substring exists in a string
		 */
		public static function is_in_string(
			string $search,
			string $string
		): bool {
			return strpos($string, $search) !== false ? true : false;
		}


		/**
		 * Search for the allowed tags in the content and display them
		 */
		public static function strip_html(
			string $content,
			$allow=""
		): string {
			return strip_tags($content, $allow);
		}


		/**
		 * Replace all values in a text
		 */
		public static function text_replace(
			string $text,
			array $params=[]
		): string {
			return str_replace(
				array_keys($params),
				array_values($params),
				$text
			);
		}


		/**
		 * Separate Camel Case String
		 */
		public static function split_camelcase_string(
			string $str,
			string $split=" "
		): string {
			$pieces = preg_split("/(?=[A-Z])/", $str);
			return trim(implode($split, $pieces));
		}


		/**
		 * Get the string value safely
		 */
		public static function get_string_safe(
			?string $str
		): string {
			if (self::string_null_or_empty($str)) {
				return "";
			}
			return $str;
		}


		/**
		 * Generates a Class Name from the Given String
		 */
		public static function generate_class_name_from_string(
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
		public static function safe_name(
			string $str,
			string $trim_char="-"
		): string {
			$safe_name = htmlentities($str, ENT_COMPAT, "UTF-8", false);
			$safe_name = preg_replace('/&([a-z]{1,2})(?:acute|lig|grave|ring|tilde|uml|cedil|caron);/i','\1',$safe_name);
			$safe_name = html_entity_decode($safe_name, ENT_COMPAT, "UTF-8");
			$safe_name = preg_replace ( "/[^أ-يa-zA-Z0-9٠-٩_.-]/u", $trim_char, $safe_name);
			$safe_name = preg_replace('/-+/', $trim_char, $safe_name);
			$safe_name = trim($safe_name, $trim_char);

			$isArabic = self::has_arabic_char($str);
			if (!$isArabic) {
				$safe_name = strtolower($safe_name);
			}

			return $safe_name;
		}


		/**
		 * Checks if the given str contains any arabic characters
		 */
		public static function has_arabic_char(
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

			return $arabic_count > 0;
		}


		/**
		 * Converts the given String into an Array
		 */
		public static function explode_str_to_arr(
			?string $str,
			string $delimiter="",
			int $chunk_length=0
		): array {
			if (self::string_null_or_empty($str)) {
				return [];
			}

			if (!self::string_null_or_empty($delimiter)) {
				return explode($delimiter, $str);
			}

			if ($chunk_length > 0) {
				$arr = [];
				while (strlen($str) > $chunk_length) {
					$chunk = substr($str, 0, $chunk_length);

					$arr[] = $chunk;
					$str   = substr($str, $chunk_length);
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
		public static function implode_arr_to_str(
			?array $array,
			string $delimiter=" "
		): string {
			if (self::array_null_or_empty($array)) {
				return "";
			}
			return implode($delimiter, self::unset_array_empty_values($array));
		}


		/**
		 * Get the value of the given key in a given array
		 */
		public static function get_value_from_arr_by_key(
			?array $arr,
			string $key=""
		): string {
			if (self::array_null_or_empty($arr) || !isset($arr[$key])) {
				return "";
			}
			return $arr[$key];
		}


		/**
		 * Unset Empty Values from the given object/array
		 */
		public static function unset_array_empty_values(
			?array $array
		): array {
			if (self::array_null_or_empty($array)) {
				return [];
			}

			return array_values(
				array_filter(
					$array,
					function($value) {
						if (!Helper::string_null_or_empty($value)) {
							return $value;
						}
					}
				)
			);
		}


		/**
		 * Generate Key Value String from Array
		 */
		public static function gererate_key_value_string_from_array(
			?array $params,
			string $key_prefix="",
			string $key_value_join="=",
			string $value_holder="\"",
			string $elements_join=" "
		): string {
			if (self::array_null_or_empty($params)) {
				return "";
			}

			$str = "";
			foreach ($params AS $k => $v) {
				$k = $key_prefix . $k;
				$str .= ($str != "" ? $elements_join : "") . $k . $key_value_join . $value_holder . $v . $value_holder;
			}
			return $str;
		}


		/**
		 * Checks if the given directory is available in the domain folders
		 */
		public static function directory_exists(
			?string $dir_name,
			string $path="./",
			bool $check_subfolders=false
		): bool {
			if (self::string_null_or_empty($dir_name)) {
				return false;
			}

			if (is_dir($path . $dir_name)) {
				return true;
			}

			if ($check_subfolders) {
				$tree = glob($path . "*", GLOB_ONLYDIR);
				if ($tree && count($tree) > 0) {
					foreach ($tree AS $dir) {
						if (self::directory_exists($dir_name, $dir . "/")) {
							return true;
						}
					}
				}
			}

			return false;
		}


		/**
		 * Create the given folder
		 */
		public static function create_folder(
			string $dir,
			string $permission="0777"
		): bool {
			if (!is_dir($dir)) {
				mkdir($dir, $permission, true);
				return true;
			}
			return false;
		}


		/**
		 * Delete the given file/folder
		 */
		public static function delete_file_or_folder(
			string $dir
		): bool {
			if (file_exists($dir)) {
				if (is_dir($dir)) {
					rmdir($dir);
					return true;
				}

				if (!is_dir($dir)) {
					unlink($dir);
					return true;
				}
			}
			return false;
		}


		/**
		 * Retreive Youtube embed id from the video full link
		 */
		public static function get_youtube_id(
			?string $url
		): string {
			if (self::string_null_or_empty($url)) {
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
		 * Encrypt a String
		 */
		public static function encrypt_string(
			?string $string
		): string {
			if (self::string_null_or_empty($string)) {
				return "";
			}
			return str_replace("&", "[amp;]", base64_encode($string));
		}


		/**
		 * Dencrypt a String
		 */
		public static function decrypt_string(
			?string $string
		): string {
			if (self::string_null_or_empty($string)) {
				return "";
			}
			return base64_decode(str_replace("[amp;]", "&", $string));
		}


		/**
		 * Get Status Class from the given code
		 */
		public static function get_status_class_from_code(
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
		public static function get_html_content_from_file(
			?string $file_path=null,
			?array $replace=null
		): string {
			if (self::string_null_or_empty($file_path)) {
				throw new NotEmptyParamException('file_path');
			}
			if (!file_exists($file_path)) {
				throw new FileNotFoundException('file_path');
			}

			$content = file_get_contents($file_path);
			if (!Helper::array_null_or_empty($replace)) {
				$content = str_replace(
					array_keys($replace),
					array_values($replace),
					$content
				);
			}
			return $content;
		}


		/**
		 * Get JSON content from the given file path
		 */
		public static function get_json_content_from_file_as_array(
			?string $file_path
		): array {
			if (self::string_null_or_empty($file_path)) {
				throw new NotEmptyParamException('file_path');
			}
			if (!file_exists($file_path)) {
				throw new FileNotFoundException('file_path');
			}
			return json_decode(file_get_contents($file_path), true);
		}


		/**
		 * Adds the root folder to a url, and converts it to a safe, user friendly URL
		 */
		public static function generate_full_url(
			string $page,
			string $lang="",
			array $safe_params=[],
			array $optional_params=[],
			string $root="",
			bool $is_safe_url=true
		) {
			$args = "";
			$finalSafe_params = [];

			if ($lang != "") {
				$finalSafe_params["lang"] = $lang;
			}

			foreach ($safe_params AS $k => $v) {
				$finalSafe_params[$k] = $v;
			}

			foreach ($finalSafe_params AS $k => $v) {
				if (!$is_safe_url) {
					$args .= $args === "" ? "?" : "&";
				}
				$args .= !$is_safe_url ? $k . "=" . $v : "/" . $v;
			}

			foreach ($optional_params as $k => $v){
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

			if (!self::string_null_or_empty($root) && !self::string_ends_with($root, "/")) {
				$root .= "/";
			}
			$url = $root . $page . $args;

			$urlScheme = "";
			if (self::string_begins_with($url, "http://")) {
				$urlScheme = "http://";
			}
			if (self::string_begins_with($url, "https://")) {
				$urlScheme = "https://";
			}

			if (!self::string_null_or_empty($urlScheme)) {
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
		public static function add_version_parameter_to_path(
			string $path,
			string $website_root,
			string $version=""
		) {
			return self::generate_full_url($path, "", [], [
				"v" => $version
			], $website_root);
		}


		/**
		 * Get all files in a path
		 */
		public static function get_all_files(
			string $path,
			bool $recursive=false
		): array {
			$files_arr = [];

			if (is_dir($path)) {
				$files = scandir($path);

				foreach ($files AS $file) {
					if (!is_dir($path . "/" . $file)) {
						$files_arr[] = $path . "/" . $file;
					}
					else {
						if ($recursive && $file !== "." && $file !== "..") {
							$files_arr = array_merge($files_arr, self::get_all_files($path . "/" . $file, $recursive));
						}
					}
				}
			}
			return $files_arr;
		}


		/**
		 * Converts a multidimentional array to a single dimentional array
		 */
		public static function convert_multidimention_array_to_single_dimention(
			array $array_to_convert,
			string $preKey=""
		): array {
			$return_array = [];

			foreach ($array_to_convert AS $k => $v) {
				if (is_array($v)) {
					$return_array = array_merge($return_array,
						self::convert_multidimention_array_to_single_dimention($v, $preKey . $k . ".")
					);
				}
				else {
					$return_array[$preKey . $k] = $v;
				}
			}
			return $return_array;
		}


		/**
		 * Add scheme to the given string if not exists
		 */
		public static function add_scheme_if_missing(
			string $string,
			string $scheme
		): string {
			if (self::string_null_or_empty($string)) {
				return "";
			}
			if (self::string_null_or_empty($scheme)) {
				return $string;
			}
			if (self::is_valid_url($string)) {
				return $string;
			}

			if (!self::string_ends_with($scheme, "://")) {
				$scheme .= "://";
			}
			return $scheme . $string;
		}


		/**
		 * Replace scheme of the given string with the given scheme
		 */
		public static function replace_scheme(
			string $string,
			string $scheme
		): string {
			if (self::string_null_or_empty($string)) {
				return "";
			}
			if (self::string_null_or_empty($scheme)) {
				return $string;
			}

			if (self::is_valid_url($string)) {
				$string = str_replace(["http://", "https://"], "", $string);
			}

			if (!self::string_ends_with($scheme, "://")) {
				$scheme .= "://";
			}
			return $scheme . $string;
		}


		/**
		 * Check if the given string is a valid link
		 */
		public static function is_valid_url(string $string): bool {
			return self::string_begins_with($string, ["http://", "https://"]);
		}

	}
