<?php
	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Helpers\StyleTest.php

	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Helpers\Style;

	class StyleTest extends TestCase {

		public function setUp(): void {
			Style::clear_files();
			Style::clear_styles();

			parent::setUp();
		}

		public function test_add_file_success() {
			$this->assertEmpty(Style::get_files());

			Style::add_file("test", "");
			$this->assertCount(1, Style::get_files());
		}

		public function test_remove_file_success() {
			$this->assertEmpty(Style::get_files());

			Style::add_file("test", "");
			$this->assertCount(1, Style::get_files());

			Style::remove_file("test");
			$this->assertEmpty(Style::get_files());
		}

		public function test_add_style_success() {
			$this->assertEmpty(Style::get_styles());

			Style::add_style("test", "");
			$this->assertCount(1, Style::get_styles());
		}

		public function test_remove_style_success() {
			$this->assertEmpty(Style::get_styles());

			Style::add_style("test", "");
			$this->assertCount(1, Style::get_styles());

			Style::remove_style("test");
			$this->assertEmpty(Style::get_styles());
		}

		public function test_get_files_includes_success() {
			Style::add_file("file_1", "file_1.css");
			Style::add_file("file_2", "file_2.css");
			Style::add_file("file_3", "file_3.css");

			Style::add_style("style_1", "<link rel=\"stylesheet\" href=\"style_1.css\">");
			Style::add_style("style_2", "<link rel=\"stylesheet\" href=\"style_2.css\">");
			Style::add_style("style_3", "<link rel=\"stylesheet\" href=\"style_3.css\">");

			$expected = Helper::get_html_content_from_file(__DIR__ . "/../../_CommonFiles/Style/styles.html");
			$actual = Style::get_files_includes();

			$this->assertEquals($expected, $actual);
		}

	}

?>