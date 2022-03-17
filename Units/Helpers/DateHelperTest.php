<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\DateHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Constants\DateFormats;
	use RawadyMario\Constants\Lang;
	use RawadyMario\Helpers\DateHelper;

	final class DateHelperTest extends TestCase {

		public function testCleanDate(): void {
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

		public function testRenderDate(): void {
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
				DateHelper::RenderDate("2022-01-07 11:35:23", DateFormats::DATETIME_FORMAT_NICE)
			);
		}

		public function testRenderDateFromTime(): void {
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

		public function testGetMonthName(): void {
			$this->assertEquals(
				"January",
				DateHelper::GetMonthName(1)
			);

			$this->assertEquals(
				"شباط",
				DateHelper::GetMonthName(2, "ar")
			);

			$this->assertEquals(
				"Août",
				DateHelper::GetMonthName(8, "fr")
			);

			$this->assertEquals(
				"October",
				DateHelper::GetMonthName(10)
			);

			$this->assertEquals(
				"November",
				DateHelper::GetMonthName(11)
			);

			$this->assertEquals(
				"December",
				DateHelper::GetMonthName(12)
			);
		}

		public function testGetWeekDayName(): void {
			$this->assertEquals(
				"Sunday",
				DateHelper::GetWeekDayName(0)
			);

			$this->assertEquals(
				"Sunday",
				DateHelper::GetWeekDayName(0)
			);

			$this->assertEquals(
				"Wednesday",
				DateHelper::GetWeekDayName(3)
			);

			$this->assertEquals(
				"الأربعاء",
				DateHelper::GetWeekDayName(3, "ar")
			);

			$this->assertEquals(
				"Jeudi",
				DateHelper::GetWeekDayName(4, "fr")
			);
		}

	}
