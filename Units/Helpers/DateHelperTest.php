<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\DateHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Models\DateFormats;
	use RawadyMario\Models\Lang;
	use RawadyMario\Helpers\DateHelper;
	use RawadyMario\Helpers\TranslateHelper;
	use RawadyMario\Models\DateFormatTypes;
	use RawadyMario\Models\DateTypes;

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
				DateHelper::RenderDate(strtotime("2022-01-07"), DateFormats::DATE_SAVE, "", true)
			);

			$this->assertEquals(
				"07/01/2022",
				DateHelper::RenderDate("2022-01-07", DateFormats::DATE_MAIN)
			);

			$this->assertEquals(
				"07/01",
				DateHelper::RenderDate("2022-01-07", DateFormats::DATE_MAIN_NO_YEAR)
			);

			$this->assertEquals(
				"07 Jan, 2022",
				DateHelper::RenderDate("2022-01-07", DateFormats::DATE_NICE)
			);

			$this->assertEquals(
				"٠٧ كانون ثاني، ٢٠٢٢",
				DateHelper::RenderDate("2022-01-07", DateFormats::DATE_NICE, Lang::AR)
			);

			$this->assertEquals(
				"11:35:23",
				DateHelper::RenderDate(strtotime("2022-01-07 11:35:23"), DateFormats::TIME_SAVE, "", true)
			);

			$this->assertEquals(
				"07 Jan, 2022 11:35",
				DateHelper::RenderDate("2022-01-07 11:35:23", DateFormats::DATETIME_NICE)
			);
		}

		public function testRenderDateFromTime(): void {
			$this->assertEquals(
				"2022-01-07",
				DateHelper::RenderDateFromTime(strtotime("2022-01-07"), DateFormats::DATE_SAVE, "")
			);

			$this->assertEquals(
				"11:35:23",
				DateHelper::RenderDateFromTime(strtotime("2022-01-07 11:35:23"), DateFormats::TIME_SAVE, "")
			);

			$this->assertEquals(
				"07 Jan, 2022 11:35",
				DateHelper::RenderDateFromTime(strtotime("2022-01-07 11:35:23"), DateFormats::DATETIME_NICE, "")
			);
		}

		public function testGetMonthName(): void {
			$this->assertEquals(
				"January",
				DateHelper::GetMonthName(1)
			);

			$this->assertEquals(
				"شباط",
				DateHelper::GetMonthName(2, Lang::AR)
			);

			$this->assertEquals(
				"Août",
				DateHelper::GetMonthName(8, Lang::FR)
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
				DateHelper::GetWeekDayName(3, Lang::AR)
			);

			$this->assertEquals(
				"Jeudi",
				DateHelper::GetWeekDayName(4, Lang::FR)
			);
		}

		public function testRenderDateExtended(): void {
			TranslateHelper::AddDefaults();

			$this->assertEquals(
				"",
				DateHelper::RenderDateExtended(null)
			);

			$this->assertEquals(
				"",
				DateHelper::RenderDateExtended("")
			);

			$this->assertEquals(
				"07 Jan, 1992",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10")
			);

			$this->assertEquals(
				"٠٧ كانون ثاني، ١٩٩٢",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10", Lang::AR)
			);

			$this->assertEquals(
				"07 Jan, 1992 05:30",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, true)
			);

			$this->assertEquals(
				"07 January, 1992",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatTypes::FULL)
			);

			$this->assertEquals(
				"yesterday",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatTypes::NICE, "1992-01-08 05:30:10")
			);

			$this->assertEquals(
				"07 Jan",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatTypes::NICE, "1992-01-08 05:30:11")
			);

			$this->assertEquals(
				"07 Jan, 05:30",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, true, DateFormatTypes::NICE, "1992-01-08 05:30:11")
			);

			$this->assertEquals(
				"tomorrow",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatTypes::NICE, "1992-01-06 05:30:10")
			);

			$this->assertEquals(
				"07 Jan",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatTypes::NICE, "1992-01-06 05:30:09")
			);

			$this->assertEquals(
				"today",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatTypes::NICE, "1992-01-07 12:00:00")
			);

			$this->assertEquals(
				"today at 05:30",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, true, DateFormatTypes::NICE, "1992-01-07 12:00:00")
			);

			$this->assertEquals(
				"اليوم في ٠٥:٣٠",
				DateHelper::RenderDateExtended("1992-01-07 05:30:10", Lang::AR, true, DateFormatTypes::NICE, "1992-01-07 12:00:00")
			);

		}

		public function testGetDaysCount(): void {
			$this->assertEquals(
				0,
				DateHelper::GetDaysCount(null, "1992-01-07")
			);

			$this->assertEquals(
				0,
				DateHelper::GetDaysCount("1992-01-07", null)
			);

			$this->assertEquals(
				1,
				DateHelper::GetDaysCount("1992-01-07", "1992-01-08")
			);

			$this->assertEquals(
				1.5,
				DateHelper::GetDaysCount("1992-01-07 05:30:00", "1992-01-08 17:30:00")
			);

			$this->assertEquals(
				1.5,
				DateHelper::GetDaysCount("1992-01-08 17:30:00", "1992-01-07 05:30:00")
			);
		}


		public function testGetFormatFromType(): void {
			$this->assertEquals(
				[
					DateTypes::DATE => DateFormats::DATE_SAVE,
					DateTypes::TIME => DateFormats::TIME_SAVE,
					DateTypes::DATETIME => DateFormats::DATETIME_SAVE,
				],
				DateHelper::GetFormatFromType()
			);

			$this->assertEquals(
				[
					DateTypes::DATE => DateFormats::DATE_SAVE,
					DateTypes::TIME => DateFormats::TIME_MAIN,
					DateTypes::DATETIME => DateFormats::DATETIME_SAVE,
				],
				DateHelper::GetFormatFromType(null, null, false)
			);

			$this->assertEquals(
				[
					DateTypes::DATE => DateFormats::DATE_MAIN,
					DateTypes::TIME => DateFormats::TIME_SAVE,
					DateTypes::DATETIME => DateFormats::DATETIME_MAIN,
				],
				DateHelper::GetFormatFromType(DateFormatTypes::MAIN, null)
			);

			$this->assertEquals(
				[
					DateTypes::DATE => DateFormats::DATE_MAIN_NO_YEAR,
					DateTypes::TIME => DateFormats::TIME_MAIN,
					DateTypes::DATETIME => DateFormats::DATETIME_MAIN_NO_YEAR,
				],
				DateHelper::GetFormatFromType(DateFormatTypes::MAIN, null, false)
			);

			$this->assertEquals(
				DateFormats::DATE_NICE,
				DateHelper::GetFormatFromType(DateFormatTypes::NICE, DateTypes::DATE)
			);

			$this->assertEquals(
				DateFormats::DATETIME_NICE_NO_YEAR,
				DateHelper::GetFormatFromType(DateFormatTypes::NICE, DateTypes::DATETIME, false)
			);
		}

	}
