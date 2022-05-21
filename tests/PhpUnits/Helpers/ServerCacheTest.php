<?php
	namespace RawadyMario\Tests\Helpers;

	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\FileNotFoundException;
	use RawadyMario\Exceptions\NotEmptyParamException;
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Helpers\ServerCache;
	use RawadyMario\Language\Helpers\Translate;

	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Helpers\ServerCacheTest.php

	class ServerCacheTest extends TestCase {
		private const CACHE_FOLDER = __DIR__ . "/../../_CommonFiles/Cache/";

		public function setUp(): void {
			self::DeleteAllGeneratedFiles();
			parent::setUp();
		}

		public function tearDown(): void {
			self::DeleteAllGeneratedFiles();
			parent::tearDown();
		}

		public function testSetWithoutCacheFolderFail(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "ServerCache::\$versionFolder"
			]));
			ServerCache::Set("test", "This is a test cache!");
		}

		public function testGetWithoutCacheFolderFail(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "ServerCache::\$versionFolder"
			]));
			ServerCache::Get("test");
		}

		public function testSetSuccess() {
			ServerCache::SetVersion("1.0.0");
			ServerCache::SetCacheFolder(self::CACHE_FOLDER);

			ServerCache::Set("test", "This is a test Cache!");

			$this->assertFileExists(
				ServerCache::GetCacheFileName("test")
			);
		}

		public function testGetFail() {
			$this->expectException(FileNotFoundException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.FileNotFound", null, [
				"::params::" => "cacheFileName"
			]));
			ServerCache::Get("test");
		}

		public function testGetAsStringSuccess() {
			$cacheContent = "This is a test Cache!";
			ServerCache::SetVersion("1.0.0");
			ServerCache::SetCacheFolder(self::CACHE_FOLDER);

			ServerCache::Set("test", $cacheContent);
			$this->assertFileExists(
				ServerCache::GetCacheFileName("test")
			);

			$actual = ServerCache::Get("test");
			$this->assertEquals($cacheContent, $actual);
		}

		public function testGetAsArraySuccess() {
			$cacheContent = [
				"Array Element 1",
				"Array Element 2",
				"Array Element 3",
			];
			ServerCache::SetVersion("1.0.0");
			ServerCache::SetCacheFolder(self::CACHE_FOLDER);

			ServerCache::Set("test", $cacheContent);
			$this->assertFileExists(
				ServerCache::GetCacheFileName("test")
			);

			$actual = ServerCache::Get("test", true);
			$this->assertEqualsCanonicalizing($cacheContent, $actual);
		}
		
		private static function DeleteAllGeneratedFiles(): void {
			$filesToDelete = Helper::GetAllFiles(self::CACHE_FOLDER, true);
			foreach ($filesToDelete AS $file) {
				Helper::DeleteFileOrFolder($file);
			}
			Helper::DeleteFileOrFolder(self::CACHE_FOLDER . "/1.0.0");
		}

	}
