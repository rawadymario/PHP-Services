<?php
	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Helpers\ScriptTest.php

	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Helpers\Script;

	class ScriptTest extends TestCase {

		public function setUp(): void {
			Script::clear_files();
			Script::clear_scripts();

			parent::setUp();
		}

		public function test_add_file_success() {
			$this->assertEmpty(Script::get_files());

			Script::add_file("test", "");
			$this->assertCount(1, Script::get_files());
		}

		public function test_remove_file_success() {
			$this->assertEmpty(Script::get_files());

			Script::add_file("test", "");
			$this->assertCount(1, Script::get_files());

			Script::remove_file("test");
			$this->assertEmpty(Script::get_files());
		}

		public function test_add_script_success() {
			$this->assertEmpty(Script::get_scripts());

			Script::add_script("test", "");
			$this->assertCount(1, Script::get_scripts());
		}

		public function test_remove_script_success() {
			$this->assertEmpty(Script::get_scripts());

			Script::add_script("test", "");
			$this->assertCount(1, Script::get_scripts());

			Script::remove_script("test");
			$this->assertEmpty(Script::get_scripts());
		}

		public function test_get_files_includes_success() {
			Script::add_file("file_1", "file_1.js");
			Script::add_file("file_2", "file_2.js");
			Script::add_file("file_3", "file_3.js");

			Script::add_script("script_1", "<script src=\"script_1.js\"></script>");
			Script::add_script("script_2", "<script src=\"script_2.js\"></script>");
			Script::add_script("script_3", "<script src=\"script_3.js\"></script>");

			$expected = Helper::get_html_content_from_file(__DIR__ . "/../../_CommonFiles/Script/scripts.html");
			$actual = Script::get_files_includes();

			$this->assertEquals($expected, $actual);
		}

	}

?>