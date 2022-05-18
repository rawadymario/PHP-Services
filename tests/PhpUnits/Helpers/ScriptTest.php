<?php
	namespace RawadyMario\Tests\Helpers;

	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Helpers\ScriptTest.php

	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Helpers\Script;

	class ScriptTest extends TestCase {

		public function setUp(): void {
			Script::ClearFiles();
			Script::ClearScripts();

			parent::setUp();
		}

		public function testAddFileSuccess() {
			$this->assertEmpty(Script::GetFiles());

			Script::AddFile("test", "");
			$this->assertCount(1, Script::GetFiles());
		}

		public function testRemoveFileSuccess() {
			$this->assertEmpty(Script::GetFiles());

			Script::AddFile("test", "");
			$this->assertCount(1, Script::GetFiles());

			Script::RemoveFile("test");
			$this->assertEmpty(Script::GetFiles());
		}

		public function testAddScriptSuccess() {
			$this->assertEmpty(Script::GetScripts());

			Script::AddScript("test", "");
			$this->assertCount(1, Script::GetScripts());
		}

		public function testRemoveScriptSuccess() {
			$this->assertEmpty(Script::GetScripts());

			Script::AddScript("test", "");
			$this->assertCount(1, Script::GetScripts());

			Script::RemoveScript("test");
			$this->assertEmpty(Script::GetScripts());
		}

		public function testGetFilesIncludesSuccess() {
			Script::AddFile("file_1", "file_1.js");
			Script::AddFile("file_2", "file_2.js");
			Script::AddFile("file_3", "file_3.js");

			Script::AddScript("script_1", "<script src=\"script_1.js\"></script>");
			Script::AddScript("script_2", "<script src=\"script_2.js\"></script>");
			Script::AddScript("script_3", "<script src=\"script_3.js\"></script>");

			$expected = Helper::GetHtmlContentFromFile(__DIR__ . "/../../_CommonFiles/Script/scripts.html");
			$actual = Script::GetFilesIncludes();

			$this->assertEquals($expected, $actual);
		}

	}

?>