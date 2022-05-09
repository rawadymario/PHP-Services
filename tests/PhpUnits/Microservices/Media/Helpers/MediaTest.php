<?php
	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Microservices\MEdia\Helpers\MediaTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\FileNotFoundException;
	use RawadyMario\Exceptions\NotEmptyParamException;
	use RawadyMario\Language\Helpers\Translate;
	use RawadyMario\Media\Helpers\Media;
	use RawadyMario\Media\Models\Image;

	final class MediaTest extends TestCase {
		private const MEDIA_ROOT = "https://media.domain.com";
		private const WEBSITE_VERSION = "2.0.1";

		public function setUp(): void {
			Media::SetVariableUploadDir(__DIR__ . "/../../../_TestsForUnits/Media");
			Media::SetVariableMediaRoot(self::MEDIA_ROOT);
			Media::SetVariableWebsiteVersion(self::WEBSITE_VERSION);

			parent::setUp();
		}

		public function testGetMediaFullPathThrowNotEmptyError_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "path"
			]));
			Media::GetMediaFullPath("");
		}

		public function testGetMediaFullPathThrowNotEmptyError_02(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "path"
			]));
			Media::GetMediaFullPath(null);
		}

		public function testGetMediaFullPathThrowFileNotFoundError(): void {
			$this->expectException(FileNotFoundException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.FileNotFound", null, [
				"::params::" => self::MEDIA_ROOT . "/users/profile/invalid-user-01.jpg"
			]));
			Media::GetMediaFullPath("mediafiles/users/profile/invalid-user-01.jpg");
		}

		public function testGetMediaFullPathSuccess(): void {
			$this->assertEquals(
				self::MEDIA_ROOT . "/users/profile/user-01.jpg?v=" . self::WEBSITE_VERSION,
				Media::GetMediaFullPath("mediafiles/users/profile/user-01.jpg")
			);

			$this->assertEquals(
				self::MEDIA_ROOT . "/users/profile/user-01-th.jpg?v=" . self::WEBSITE_VERSION,
				Media::GetMediaFullPath("mediafiles/users/profile/user-01.jpg", Image::THUMBNAIL_CODE)
			);

			$this->assertEquals(
				self::MEDIA_ROOT . "/users/profile/user-01.jpg?v=" . self::WEBSITE_VERSION,
				Media::GetMediaFullPath("mediafiles/users/profile/user-01.jpg", Image::HIGH_DEF_CODE)
			);

			$this->assertEquals(
				self::MEDIA_ROOT . "/users/profile/user-01.webp?v=" . self::WEBSITE_VERSION,
				Media::GetMediaFullPath("mediafiles/users/profile/user-01.jpg", null, true)
			);

			$this->assertEquals(
				self::MEDIA_ROOT . "/users/profile/user-01.jpg",
				Media::GetMediaFullPath("mediafiles/users/profile/user-01.jpg", null, false, false)
			);

			$this->assertEquals(
				"users/profile/user-01.jpg",
				Media::GetMediaFullPath("mediafiles/users/profile/user-01.jpg", null, false, false, false)
			);
		}

	}
