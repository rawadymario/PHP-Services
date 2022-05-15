<?php
	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Helpers\MetaSeoTest.php

	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\InvalidArgumentException;
use RawadyMario\Helpers\Helper;
use RawadyMario\Helpers\MetaSeo;

	class MetaSeoTest extends TestCase {

		public function setUp(): void {
			$metaSeoArray = MetaSeo::GetMetaArray();
			foreach ($metaSeoArray as $key => $value) {
				MetaSeo::RemoveFromMetaArray($key);
			}

			parent::setUp();
		}

		public function testAddToMetaArraySuccess() {
			$this->assertEmpty(MetaSeo::GetMetaArray());

			MetaSeo::AddToMetaArray("test", []);
			$this->assertCount(1, MetaSeo::GetMetaArray());
		}

		public function testRemoveFromMetaArraySuccess() {
			$this->assertEmpty(MetaSeo::GetMetaArray());

			MetaSeo::AddToMetaArray("test", []);
			$this->assertCount(1, MetaSeo::GetMetaArray());

			MetaSeo::RemoveFromMetaArray("test");
			$this->assertEmpty(MetaSeo::GetMetaArray());
		}

		public function testInvalidArgumentFail() {
			MetaSeo::AddToMetaArray("test", []);

			$this->expectException(InvalidArgumentException::class);
			$this->expectExceptionMessage("Invalid argument \"type\" having the value \"\". Allowed value(s): \"meta, comment\"");
			MetaSeo::RenderFull();
		}

		public function testRenderFullSuccess() {
			MetaSeo::SetClientName("Mario Rawady");
			MetaSeo::SetPreTitle("Software Engineer");
			MetaSeo::SetPostTitle("Home Page");
			MetaSeo::SetTitle("Mario Rawady");
			MetaSeo::SetAuthor("Mario Rawady");
			MetaSeo::SetKeywords([
				"Mario",
				"Rawady",
				"Software Engineer",
				"PHP",
			]);
			MetaSeo::SetDescription("Mario Rawady is a Software Engineer");
			MetaSeo::SetPhoto("https://rawadymario.com/assets/img/logo-big.png");
			MetaSeo::SetUrl("https://rawadymario.com");
			MetaSeo::SetRobots(true);
			MetaSeo::SetGoolgeSiteVerification("");
			MetaSeo::SetCopyright("2022. Mario Rawady");
			MetaSeo::SetFacebookAppId("123456789");
			MetaSeo::SetFacebookAdmins("");
			MetaSeo::SetTwitterCard("testtt"); //Should default to: summary_large_image
			MetaSeo::SetFavicon("https://rawadymario.com/assets/img/favicon.png");

			MetaSeo::AddToMetaArray("test", [
				"type" => "meta",
				"name" => "test",
				"content" => "This is a test text"
			]);

			MetaSeo::AddToPreHeadArray("pre_1", "<!-- Here Goes Pre Head Scripts 01 -->");
			MetaSeo::AddToPreHeadArray("pre_2", "<!-- Here Goes Pre Head Scripts 02 -->");

			MetaSeo::AddToPostHeadArray("post_1", "<!-- Here Goes Post Head Scripts 01 -->");
			MetaSeo::AddToPostHeadArray("post_2", "<!-- Here Goes Post Head Scripts 02 -->");

			$expected = Helper::GetHtmlContentFromFile(__DIR__ . "/../../_CommonFiles/MetaSeo/header.html");
			$actual = MetaSeo::RenderFull();

			$this->assertEquals($expected, $actual);
		}

	}

?>