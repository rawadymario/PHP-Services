<?php
	//To Run: .\vendor/bin/phpunit .\Units\Automated\Helpers\MediaHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\FileNotFoundException;
	use RawadyMario\Exceptions\NotEmptyParamException;
	use RawadyMario\Helpers\MediaHelper;
	use RawadyMario\Helpers\TranslateHelper;

	final class MediaHelperTest extends TestCase {
		private const MEDIA_ROOT = "https://media.domain.com";
		private const WEBSITE_VERSION = "2.0.1";

		public function setUp(): void {
			MediaHelper::SetVariableUploadDir(__DIR__ . "/../../_TestsForUnits/Media");
			MediaHelper::SetVariableMediaRoot(self::MEDIA_ROOT);
			MediaHelper::SetVariableWebsiteVersion(self::WEBSITE_VERSION);

			parent::setUp();
		}

		public function testGetMediaFullPathThrowNotEmptyError_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(TranslateHelper::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "path"
			]));
			MediaHelper::GetMediaFullPath("");
		}

		public function testGetMediaFullPathThrowNotEmptyError_02(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(TranslateHelper::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "path"
			]));
			MediaHelper::GetMediaFullPath(null);
		}

		public function testGetMediaFullPathThrowFileNotFoundError(): void {
			$this->expectException(FileNotFoundException::class);
			$this->expectExceptionMessage(TranslateHelper::TranslateString("exception.FileNotFound", null, [
				"::params::" => self::MEDIA_ROOT . "/users/profile/invalid-user-01.jpg"
			]));
			MediaHelper::GetMediaFullPath("mediafiles/users/profile/invalid-user-01.jpg");
		}

		public function testGetMediaFullPathSuccess(): void {
			$this->assertEquals(
				self::MEDIA_ROOT . "/users/profile/user-01.jpg?v=" . self::WEBSITE_VERSION,
				MediaHelper::GetMediaFullPath("mediafiles/users/profile/user-01.jpg")
			);

			$this->assertEquals(
				self::MEDIA_ROOT . "/users/profile/user-01-th.jpg?v=" . self::WEBSITE_VERSION,
				MediaHelper::GetMediaFullPath("mediafiles/users/profile/user-01.jpg", "th")
			);

			$this->assertEquals(
				self::MEDIA_ROOT . "/users/profile/user-01.jpg?v=" . self::WEBSITE_VERSION,
				MediaHelper::GetMediaFullPath("mediafiles/users/profile/user-01.jpg", "hd")
			);

			$this->assertEquals(
				self::MEDIA_ROOT . "/users/profile/user-01.webp?v=" . self::WEBSITE_VERSION,
				MediaHelper::GetMediaFullPath("mediafiles/users/profile/user-01.jpg", null, true)
			);

			$this->assertEquals(
				self::MEDIA_ROOT . "/users/profile/user-01.jpg",
				MediaHelper::GetMediaFullPath("mediafiles/users/profile/user-01.jpg", null, false, false)
			);

			$this->assertEquals(
				"users/profile/user-01.jpg",
				MediaHelper::GetMediaFullPath("mediafiles/users/profile/user-01.jpg", null, false, false, false)
			);
		}

	}
