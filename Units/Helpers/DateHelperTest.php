<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\DateHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Constants\DateFormats;
	use RawadyMario\Helpers\DateHelper;

	final class DateHelperTest extends TestCase {
		
		public function testCleanDateSuccess(): void {
			$this->assertEquals(
				"null",
				DateHelper::CleanDate(null)
			);
			
			$this->assertEquals(
				"null",
				DateHelper::CleanDate("")
			);
			
			$this->assertEquals(
				"'1992-01-07'",
				DateHelper::CleanDate("1992-01-07")
			);
		}
		
		public function testRenderDateSuccess(): void {
			$this->assertEquals(
				"",
				DateHelper::RenderDate(null)
			);
			
			$this->assertEquals(
				"",
				DateHelper::RenderDate("")
			);
			
			$this->assertEquals(
				"2022-01-07",
				DateHelper::RenderDate("2022-01-07")
			);
			
			$this->assertEquals(
				"2022-01-07",
				DateHelper::RenderDate(strtotime("2022-01-07"), DateFormats::DATE_FORMAT_SAVE, "", true)
			);
			
			$this->assertEquals(
				"07/01/2022",
				DateHelper::RenderDate("2022-01-07", DateFormats::DATE_FORMAT_MAIN)
			);
			
			$this->assertEquals(
				"07/01",
				DateHelper::RenderDate("2022-01-07", DateFormats::DATE_FORMAT_MAIN_NO_YEAR)
			);
			
			$this->assertEquals(
				"07 Jan, 2022",
				DateHelper::RenderDate("2022-01-07", DateFormats::DATE_FORMAT_NICE)
			);
			
			$this->assertEquals(
				"٠٧ كانون ثاني، ٢٠٢٢",
				DateHelper::RenderDate("2022-01-07", DateFormats::DATE_FORMAT_NICE, "ar")
			);

			$this->assertEquals(
				"11:35:23",
				DateHelper::RenderDate(strtotime("2022-01-07 11:35:23"), DateFormats::TIME_FORMAT_SAVE, "", true)
			);

			$this->assertEquals(
				"07 Jan, 2022 11:35",
				DateHelper::RenderDate(strtotime("2022-01-07 11:35:23"), DateFormats::DATETIME_FORMAT_NICE, "", true)
			);
		}
		
		public function testRenderDateFromTimeSuccess(): void {
			$this->assertEquals(
				"2022-01-07",
				DateHelper::RenderDateFromTime(strtotime("2022-01-07"), DateFormats::DATE_FORMAT_SAVE, "")
			);
			
			$this->assertEquals(
				"11:35:23",
				DateHelper::RenderDateFromTime(strtotime("2022-01-07 11:35:23"), DateFormats::TIME_FORMAT_SAVE, "")
			);

			$this->assertEquals(
				"07 Jan, 2022 11:35",
				DateHelper::RenderDateFromTime(strtotime("2022-01-07 11:35:23"), DateFormats::DATETIME_FORMAT_NICE, "")
			);
		}
		
	}
	