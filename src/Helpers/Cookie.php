<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Exceptions\InvalidCookieException;

	class Cookie {
		private static string $prefix = "rm_";
		private static int $expire = 0;
		private static string $path = "/";
		private static string $domain = "";
		private static bool $secure = false;
		private static bool $httpOnly = false;


		/**
		 * Set cookie value
		 */
		public static function Set(
			string $key,
			string $value
		): bool {
			$name = self::$prefix . $key;
			return setcookie(
				$name,
				$value,
				self::GetExpire(),
				self::GetPath(),
				self::GetDomain(),
				self::GetSecure(),
				self::GetHttpOnly()
			);
		}

		/**
		 * Get saved value from cookie
		 */
		public static function Get(
			string $key
		): string {
			$name = self::$prefix . $key;
			if (isset($_COOKIE[$name])) {
				return $_COOKIE[$name];
			}
			throw new InvalidCookieException($name);
		}

		/**
		 * Get saved value from cookie, and then destroys the cookie
		 */
		public static function Pull(
			string $key
		): string {
			try {
				$name = self::$prefix . $key;
				$cookie = self::Get($key);
				setcookie(
					$name,
					null,
					time() - 3600
				);
				return $cookie;
			}
			catch (InvalidCookieException $e) {
				throw $e;
			}
		}

		/**
		 * Destroys the cookie
		 */
		public static function Destroy(
			string $key
		): void {
			try {
				$name = self::$prefix . $key;
				self::Get($key);
				setcookie(
					$name,
					null,
					time() - 3600
				);
			}
			catch (InvalidCookieException $e) {
				throw $e;
			}
		}

		public static function SetExpireInUnix(
			int $unix
		): void {
			self::$expire = $unix;
		}

		public static function SetExpireInDays(
			int $days
		): void {
			self::$expire = time() + (60 * 60 * 24 * $days);
		}

		public static function GetExpire(): int {
			if (self::$expire === 0) {
				self::$expire = time() + (60 * 60 * 24 * 365);
			}
			return self::$expire;
		}

		public static function SetPrefix(
			string $prefix=""
		): void {
			self::$prefix = $prefix;
		}

		public static function GetPrefix(): string {
			return self::$prefix;
		}

		public static function SetPath(
			string $path
		): void {
			self::$path = $path;
		}

		public static function GetPath(): string {
			return self::$path;
		}

		public static function SetDomain(
			string $domain
		): void {
			self::$domain = $domain;
		}

		public static function GetDomain(): string {
			return self::$domain;
		}

		public static function SetSecure(
			bool $secure
		): void {
			self::$secure = $secure;
		}

		public static function GetSecure(): bool {
			return self::$secure;
		}

		public static function SetHttpOnly(
			bool $httpOnly
		): void {
			self::$httpOnly = $httpOnly;
		}

		public static function GetHttpOnly(): bool {
			return self::$httpOnly;
		}

	}