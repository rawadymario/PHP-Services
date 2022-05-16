<?php
	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Helpers\CookieTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\InvalidCookieException;
	use RawadyMario\Helpers\Cookie;

	final class CookieTest extends TestCase {

		public function testSettersAndGettersSuccess() {
			$now = time();

			Cookie::set_expire_in_unix(time());
			$this->assertEquals($now, Cookie::get_expire());

			Cookie::set_expire_in_days(10);
			$this->assertEquals($now + (60 * 60 * 24 * 10), Cookie::get_expire());

			Cookie::set_expire_in_unix(0);
			$this->assertEquals(time() + (60 * 60 * 24 * 365), Cookie::get_expire());

			$this->assertEquals("rm_", Cookie::get_prefix());

			Cookie::set_prefix("testcookie_");
			$this->assertEquals("testcookie_", Cookie::get_prefix());

			$this->assertEquals("/", Cookie::get_path());

			Cookie::set_path("/test/path/");
			$this->assertEquals("/test/path/", Cookie::get_path());

			Cookie::set_domain("testcookie.com");
			$this->assertEquals("testcookie.com", Cookie::get_domain());

			$this->assertFalse(Cookie::get_secure());

			Cookie::set_secure(true);
			$this->assertTrue(Cookie::get_secure());

			$this->assertFalse(Cookie::get_http_only());

			Cookie::set_http_only(true);
			$this->assertTrue(Cookie::get_http_only());
		}

		public function testGetCookieThrowError() {
			$this->expectException(InvalidCookieException::class);
			Cookie::get("invalid");
		}
	}
