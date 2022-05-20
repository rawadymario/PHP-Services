<?php
	namespace RawadyMario\Tests\Helpers;

	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Helpers\StyleTest.php

	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Helpers\Style;

	class StyleTest extends TestCase {

		public function setUp(): void {
			Style::ClearFiles();
			Style::ClearStyles();

			parent::setUp();
		}

		public function testAddFileSuccess() {
			$this->assertEmpty(Style::GetFiles());

			Style::AddFile("test", "");
			$this->assertCount(1, Style::GetFiles());
		}

		public function testRemoveFileSuccess() {
			$this->assertEmpty(Style::GetFiles());

			Style::AddFile("test", "");
			$this->assertCount(1, Style::GetFiles());

			Style::RemoveFile("test");
			$this->assertEmpty(Style::GetFiles());
		}

		public function testAddStyleSuccess() {
			$this->assertEmpty(Style::GetStyles());

			Style::AddStyle("test", "");
			$this->assertCount(1, Style::GetStyles());
		}

		public function testRemoveStyleSuccess() {
			$this->assertEmpty(Style::GetStyles());

			Style::AddStyle("test", "");
			$this->assertCount(1, Style::GetStyles());

			Style::RemoveStyle("test");
			$this->assertEmpty(Style::GetStyles());
		}

		public function testGetFilesIncludesSuccess() {
			Style::AddFile("file_1", "file_1.css");
			Style::AddFile("file_2", "file_2.css");
			Style::AddFile("file_3", "file_3.css");

			Style::AddStyle("style_1", "<link rel=\"stylesheet\" href=\"style_1.css\">");
			Style::AddStyle("style_2", "<link rel=\"stylesheet\" href=\"style_2.css\">");
			Style::AddStyle("style_3", "<link rel=\"stylesheet\" href=\"style_3.css\">");

			$expected = Helper::GetContentFromFile(__DIR__ . "/../../_CommonFiles/Style/styles.html");
			$actual = Style::GetFilesIncludes();

			$this->assertEquals($expected, $actual);
		}

	}

?>