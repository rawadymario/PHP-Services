<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Exceptions\InvalidCookieException;

	class Cookie {
		protected static string $prefix = "rm_";
		protected static int $expire = 0;
		protected static string $path = "/";
		protected static string $domain = "";
		protected static bool $secure = false;
		protected static bool $http_only = false;


		/**
		 * Set cookie value
		 */
		public static function set(
			string $key,
			string $value
		): bool {
			$name = self::get_prefix() . $key;
			return setcookie(
				$name,
				$value,
				self::get_expire(),
				self::get_path(),
				self::get_domain(),
				self::get_secure(),
				self::get_http_only()
			);
		}

		/**
		 * Get saved value from cookie
		 */
		public static function get(
			string $key
		): string {
			$name = self::get_prefix() . $key;
			if (isset($_COOKIE[$name])) {
				return $_COOKIE[$name];
			}
			throw new InvalidCookieException($name);
		}

		/**
		 * Get saved value from cookie, and then destroys the cookie
		 */
		public static function pull(
			string $key
		): string {
			try {
				$name = self::get_prefix() . $key;
				$cookie = self::get($key);
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
		public static function destroy(
			string $key
		): void {
			try {
				$name = self::get_prefix() . $key;
				self::get($key);
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

		public static function set_expire_in_unix(
			int $unix
		): void {
			self::$expire = $unix;
		}

		public static function set_expire_in_days(
			int $days
		): void {
			self::$expire = time() + (60 * 60 * 24 * $days);
		}

		public static function get_expire(): int {
			if (self::$expire === 0) {
				self::$expire = time() + (60 * 60 * 24 * 365);
			}
			return self::$expire;
		}

		public static function set_prefix(
			string $prefix
		): void {
			self::$prefix = $prefix;
		}

		public static function get_prefix(): string {
			return self::$prefix;
		}

		public static function set_path(
			string $path
		): void {
			self::$path = $path;
		}

		public static function get_path(): string {
			return self::$path;
		}

		public static function set_domain(
			string $domain
		): void {
			self::$domain = $domain;
		}

		public static function get_domain(): string {
			return self::$domain;
		}

		public static function set_secure(
			bool $secure
		): void {
			self::$secure = $secure;
		}

		public static function get_secure(): bool {
			return self::$secure;
		}

		public static function set_http_only(
			bool $http_only
		): void {
			self::$http_only = $http_only;
		}

		public static function get_http_only(): bool {
			return self::$http_only;
		}

	}