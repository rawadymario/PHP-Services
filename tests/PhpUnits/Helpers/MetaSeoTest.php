<?php
	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Helpers\MetaSeoTest.php

	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\InvalidArgumentException;
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Helpers\MetaSeo;
	use RawadyMario\Helpers\Script;
	use RawadyMario\Helpers\Style;

	class MetaSeoTest extends TestCase {

		public function setUp(): void {
			MetaSeo::clear_meta_array();
			MetaSeo::clear_pre_head_array();
			MetaSeo::clear_post_head_array();

			parent::setUp();
		}

		public function test_add_to_meta_array_success() {
			$this->assertEmpty(MetaSeo::get_meta_array());

			MetaSeo::add_to_meta_array("test", []);
			$this->assertCount(1, MetaSeo::get_meta_array());
		}

		public function test_remove_from_meta_array_success() {
			$this->assertEmpty(MetaSeo::get_meta_array());

			MetaSeo::add_to_meta_array("test", []);
			$this->assertCount(1, MetaSeo::get_meta_array());

			MetaSeo::remove_from_meta_array("test");
			$this->assertEmpty(MetaSeo::get_meta_array());
		}

		public function test_InvalidArgument_fail() {
			MetaSeo::add_to_meta_array("test", []);

			$this->expectException(InvalidArgumentException::class);
			$this->expectExceptionMessage("Invalid argument \"type\" having the value \"\". Allowed value(s): \"meta, comment\"");
			MetaSeo::render_full();
		}

		public function test_RenderFull_success() {
			MetaSeo::set_client_name("Mario Rawady");
			MetaSeo::set_pre_title("Software Engineer");
			MetaSeo::set_post_title("Home Page");
			MetaSeo::set_title("Mario Rawady");
			MetaSeo::set_author("Mario Rawady");
			MetaSeo::set_keywords([
				"Mario",
				"Rawady",
				"Software Engineer",
				"PHP",
			]);
			MetaSeo::set_description("Mario Rawady is a Software Engineer");
			MetaSeo::set_photo("https://rawadymario.com/assets/img/logo-big.png");
			MetaSeo::set_url("https://rawadymario.com");
			MetaSeo::set_robots(true);
			MetaSeo::set_google_site_verification("");
			MetaSeo::set_copyright("2022. Mario Rawady");
			MetaSeo::set_facebook_app_id("123456789");
			MetaSeo::set_facebook_admins("");
			MetaSeo::set_twitter_card("testtt"); //Should default to: summary_large_image
			MetaSeo::set_favicon("https://rawadymario.com/assets/img/favicon.png");

			MetaSeo::add_to_meta_array("test", [
				"type" => "meta",
				"name" => "test",
				"content" => "This is a test text"
			]);

			MetaSeo::add_to_pre_head_array("pre_1", "<!-- Here Goes Pre Head Scripts 01 -->");
			MetaSeo::add_to_pre_head_array("pre_2", "<!-- Here Goes Pre Head Scripts 02 -->");

			MetaSeo::add_to_post_head_array("post_1", "<!-- Here Goes Post Head Scripts 01 -->");
			MetaSeo::add_to_post_head_array("post_2", "<!-- Here Goes Post Head Scripts 02 -->");

			Style::AddFile("file_1", "file_1.css");
			Style::AddFile("file_2", "file_2.css");
			Style::AddStyle("style_1", "<link rel=\"stylesheet\" href=\"style_1.css\">");
			Style::AddStyle("style_2", "<link rel=\"stylesheet\" href=\"style_2.css\">");

			Script::AddFile("file_1", "file_1.js");
			Script::AddFile("file_2", "file_2.js");
			Script::AddScript("script_1", "<script src=\"script_1.js\"></script>");
			Script::AddScript("script_2", "<script src=\"script_2.js\"></script>");

			$expected = Helper::get_html_content_from_file(__DIR__ . "/../../_CommonFiles/MetaSeo/header.html");
			$actual = MetaSeo::render_full();

			$this->assertEquals($expected, $actual);
		}

	}

?>