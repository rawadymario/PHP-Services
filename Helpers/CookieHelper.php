<?php
	namespace RawadyMario\Helpers;

	class CookieHelper {
		private int $time;
		private string $path;
		private string $domain;

		private string $cookieName;

		public function __construct(
			?string $name=null,
			?int $time=null,
			?string $path=null,
			?string $domain=null
		) {
			if ($time === null) {
				$time = time() + (60 * 60 * 24 * 30); //30 Days
			}
			if (Helper::StringNullOrEmpty($path)) {
				$path = "/";
			}
			if (Helper::StringNullOrEmpty($domain)) {
				$domain = "";
			}

			$this->SetName($name);
			$this->SetTime($time);
			$this->SetPath($path);
			$this->SetDomain($domain);
		}

		public function SetName(
			string $var
		): void {
			$this->cookieName = $var;
		}

		public function SetTime(
			int $var
		): void {
			$this->time = $var;
		}

		public function SetPath(
			string $var
		): void {
			$this->path = $var;
		}

		public function SetDomain(
			string $var
		): void {
			$this->domain = $var;
		}

		public function SetCookie(
			$value
		) {
			setcookie(
				$this->cookieName,
				$value,
				$this->time,
				$this->path,
				$this->domain);
		}

		public function GetCookie(
			$default
		) {
			if (isset($_COOKIE[$this->cookieName])) {
				return $_COOKIE[$this->cookieName];
			}
			return $default;
		}

		public function ClearCookie() {
			if (isset($_COOKIE[$this->cookieName])) {
				setcookie(
					$this->cookieName,
					null,
					0,
					$this->path,
					$this->domain);
			}
			return true;
		}

	}