<?php
	namespace RawadyMario\Helpers;

	class MachineInfo {

		public static function get_all_info(): array {
			return [
				"user_agent" => self::get_user_agent(),
				"os" => self::get_operating_system(),
				"browser" => self::get_browser(),
				"ip_address" => self::get_ip_address(),
				"ip_info" => self::get_ip_info("185.126.44.43"),
			];
		}


		/**
		 * Get the user agent
		 */
		public static function get_user_agent(): string {
			return $_SERVER["HTTP_USER_AGENT"];
		}


		/**
		 * Get the user Operating System
		 */
		public static function get_operating_system(): string {
			$userAgent = self::get_user_agent();
			$array = [
				"/windows nt 10/i"		=> "Windows 10",
				"/windows nt 6.3/i"		=> "Windows 8.1",
				"/windows nt 6.2/i"		=> "Windows 8",
				"/windows nt 6.1/i"		=> "Windows 7",
				"/windows nt 6.0/i"		=> "Windows Vista",
				"/windows nt 5.2/i"		=> "Windows Server 2003/XP x64",
				"/windows nt 5.1/i"		=> "Windows XP",
				"/windows xp/i"			=> "Windows XP",
				"/windows nt 5.0/i"		=> "Windows 2000",
				"/windows me/i"			=> "Windows ME",
				"/win98/i"				=> "Windows 98",
				"/win95/i"				=> "Windows 95",
				"/win16/i"				=> "Windows 3.11",
				"/macintosh|mac os x/i"	=> "Mac OS X",
				"/mac_powerpc/i"		=> "Mac OS 9",
				"/linux/i"				=> "Linux",
				"/ubuntu/i"				=> "Ubuntu",
				"/iphone/i"				=> "iPhone",
				"/ipod/i"				=> "iPod",
				"/ipad/i"				=> "iPad",
				"/android/i"			=> "Android",
				"/blackberry/i"			=> "BlackBerry",
				"/webos/i"				=> "Mobile"
			];

			foreach ($array as $regex => $value) {
				if (preg_match($regex, $userAgent)) {
					return $value;
				}
			}
			return "Unknown OS Platform";
		}


		/**
		 * Get the user Browser Type
		 */
		public static function get_browser(): string {
			$userAgent = self::get_user_agent();
			$array = [
				"/msie/i"		=> "Internet Explorer",
				"/firefox/i"	=> "Firefox",
				"/chrome/i"		=> "Chrome",
				"/safari/i"		=> "Safari",
				"/edge/i"		=> "Edge",
				"/opera/i"		=> "Opera",
				"/netscape/i"	=> "Netscape",
				"/maxthon/i"	=> "Maxthon",
				"/konqueror/i"	=> "Konqueror",
				"/mobile/i"		=> "Handheld Browser"
			];

			foreach ($array AS $regex => $value) {
				if (preg_match($regex, $userAgent)) {
					return $value;
				}
			}
			return "Unknown Browser";
		}


		/**
		 * Get the user IP Address
		 */
		public static function get_ip_address(): string {
			/* Check for shared internet/ISP IP */
			if (!empty($_SERVER["HTTP_CLIENT_IP"]) && self::ValidateIp($_SERVER["HTTP_CLIENT_IP"])) {
				return $_SERVER["HTTP_CLIENT_IP"];
			}

			/* Check for IPs passing through proxies */
			if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
				/* Check if multiple IP addresses exist in var */
				$iplist = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]);
				foreach ($iplist as $ip) {
					if (self::ValidateIp($ip)) {
						return $ip;
					}
				}
			}

			if (!empty($_SERVER["HTTP_X_FORWARDED"]) && self::ValidateIp($_SERVER["HTTP_X_FORWARDED"])) {
				return $_SERVER["HTTP_X_FORWARDED"];
			}
			if (!empty($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"]) && self::ValidateIp($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"])) {
				return $_SERVER["HTTP_X_CLUSTER_CLIENT_IP"];
			}
			if (!empty($_SERVER["HTTP_FORWARDED_FOR"]) && self::ValidateIp($_SERVER["HTTP_FORWARDED_FOR"])) {
				return $_SERVER["HTTP_FORWARDED_FOR"];
			}
			if (!empty($_SERVER["HTTP_FORWARDED"]) && self::ValidateIp($_SERVER["HTTP_FORWARDED"])) {
				return $_SERVER["HTTP_FORWARDED"];
			}

			/* Return unreliable IP address since all else failed */
			return $_SERVER["REMOTE_ADDR"];
		}


		/**
		 * Ensures an IP address is both a valid IP address and does not fall within a private network range.
		 */
		private static function ValidateIp(
			string $ip
		): bool {
			if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
				return false;
			}
			return true;
		}


		/**
		 * Returns all the IP Address Info from http://ipinfo.io API
		 */
		public static function get_ip_info(
			?string $ip=null
		): array {
			if (Helper::string_null_or_empty($ip)) {
				$ip = self::get_ip_address();
			}

			// $ipInfo = new IpInfo($ip);

			// if ($ipInfo->count == 0 || trim($ipInfo->row["response"]) == "") {
				$url = "http://ipinfo.io/{$ip}/json";
				$ret = file_get_contents($url);

				// $ipInfo->insertUpdate([
				// 	"ip_address"	=> $ip,
				// 	"response"		=> $ret,
				// 	"created_on"	=> date(DateHelper::DATETIME_FORMAT_SAVE)
				// ]);
			// }
			// else {
			// 	$ret = $ipInfo->row["response"];
			// }

			return json_decode($ret, true);
		}

	}