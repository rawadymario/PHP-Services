<?php
	namespace RawadyMario\Tests\Date\Helpers;

	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Microservices\Date\Helpers\DateTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\InvalidParamException;
	use RawadyMario\Exceptions\NotEmptyParamException;
	use RawadyMario\Date\Models\DateFormat;
	use RawadyMario\Language\Models\Lang;
	use RawadyMario\Date\Helpers\Date;
	use RawadyMario\Language\Helpers\Translate;
	use RawadyMario\Date\Models\DateFormatType;
	use RawadyMario\Date\Models\DateType;

	final class DateTest extends TestCase {

		public function testCleanDateSuccess(): void {
			$this->assertEquals(
				"null",
				Date::CleanDate(null)
			);

			$this->assertEquals(
				"null",
				Date::CleanDate("")
			);

			$this->assertEquals(
				"'1992-01-07'",
				Date::CleanDate("1992-01-07")
			);
		}

		public function testRenderDateThrowError_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "date"
			]));
			Date::RenderDate(null);
		}

		public function testRenderDateThrowError_02(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "date"
			]));
			Date::RenderDate("");
		}

		public function testRenderDateSuccess(): void {
			$this->assertEquals(
				"2022-01-07",
				Date::RenderDate("2022-01-07")
			);

			$this->assertEquals(
				"2022-01-07",
				Date::RenderDate(strtotime("2022-01-07"), DateFormat::DATE_SAVE, "", true)
			);

			$this->assertEquals(
				"07/01/2022",
				Date::RenderDate("2022-01-07", DateFormat::DATE_MAIN)
			);

			$this->assertEquals(
				"07/01",
				Date::RenderDate("2022-01-07", DateFormat::DATE_MAIN_NO_YEAR)
			);

			$this->assertEquals(
				"07 Jan, 2022",
				Date::RenderDate("2022-01-07", DateFormat::DATE_NICE)
			);

			$this->assertEquals(
				"٠٧ كانون ثاني، ٢٠٢٢",
				Date::RenderDate("2022-01-07", DateFormat::DATE_NICE, Lang::AR)
			);

			$this->assertEquals(
				"11:35:23",
				Date::RenderDate(strtotime("2022-01-07 11:35:23"), DateFormat::TIME_SAVE, "", true)
			);

			$this->assertEquals(
				"07 Jan, 2022 11:35",
				Date::RenderDate("2022-01-07 11:35:23", DateFormat::DATETIME_NICE)
			);
		}

		public function testRenderDateFromTimeSuccess(): void {
			$this->assertEquals(
				"2022-01-07",
				Date::RenderDateFromTime(strtotime("2022-01-07"), DateFormat::DATE_SAVE, "")
			);

			$this->assertEquals(
				"11:35:23",
				Date::RenderDateFromTime(strtotime("2022-01-07 11:35:23"), DateFormat::TIME_SAVE, "")
			);

			$this->assertEquals(
				"07 Jan, 2022 11:35",
				Date::RenderDateFromTime(strtotime("2022-01-07 11:35:23"), DateFormat::DATETIME_NICE, "")
			);
		}

		public function testGetMonthNameThrowError_01(): void {
			$this->expectException(InvalidParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.InvalidParam", null, [
				"::params::" => "month"
			]));
			Date::GetMonthName(0);
		}

		public function testGetMonthNameThrowError_02(): void {
			$this->expectException(InvalidParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.InvalidParam", null, [
				"::params::" => "month"
			]));
			Date::GetMonthName(13);
		}

		public function testGetMonthNameSuccess(): void {
			$this->assertEquals(
				"January",
				Date::GetMonthName(1)
			);

			$this->assertEquals(
				"شباط",
				Date::GetMonthName(2, Lang::AR)
			);

			$this->assertEquals(
				"Août",
				Date::GetMonthName(8, Lang::FR)
			);

			$this->assertEquals(
				"October",
				Date::GetMonthName(10)
			);

			$this->assertEquals(
				"November",
				Date::GetMonthName(11)
			);

			$this->assertEquals(
				"December",
				Date::GetMonthName(12)
			);
		}

		public function testGetWeekDayNameSuccess(): void {
			$this->assertEquals(
				"Sunday",
				Date::GetWeekDayName(0)
			);

			$this->assertEquals(
				"Sunday",
				Date::GetWeekDayName(7)
			);

			$this->assertEquals(
				"Wednesday",
				Date::GetWeekDayName(3)
			);

			$this->assertEquals(
				"الأربعاء",
				Date::GetWeekDayName(3, Lang::AR)
			);

			$this->assertEquals(
				"Jeudi",
				Date::GetWeekDayName(4, Lang::FR)
			);
		}

		public function testRenderDateExtendedThrowError_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "date"
			]));
			Date::RenderDateExtended(null);
		}

		public function testRenderDateExtendedThrowError_02(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "date"
			]));
			Date::RenderDateExtended("");
		}

		public function testRenderDateExtendedSuccess(): void {
			Translate::AddDefaults();

			$this->assertEquals(
				"07 Jan, 1992",
				Date::RenderDateExtended("1992-01-07 05:30:10")
			);

			$this->assertEquals(
				"٠٧ كانون ثاني، ١٩٩٢",
				Date::RenderDateExtended("1992-01-07 05:30:10", Lang::AR)
			);

			$this->assertEquals(
				"07 Jan, 1992 05:30",
				Date::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, true)
			);

			$this->assertEquals(
				"07 January, 1992",
				Date::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatType::FULL)
			);

			$this->assertEquals(
				"yesterday",
				Date::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatType::NICE, "1992-01-08 05:30:10")
			);

			$this->assertEquals(
				"07 Jan",
				Date::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatType::NICE, "1992-01-08 05:30:11")
			);

			$this->assertEquals(
				"07 Jan, 05:30",
				Date::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, true, DateFormatType::NICE, "1992-01-08 05:30:11")
			);

			$this->assertEquals(
				"tomorrow",
				Date::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatType::NICE, "1992-01-06 05:30:10")
			);

			$this->assertEquals(
				"07 Jan",
				Date::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatType::NICE, "1992-01-06 05:30:09")
			);

			$this->assertEquals(
				"today",
				Date::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, false, DateFormatType::NICE, "1992-01-07 12:00:00")
			);

			$this->assertEquals(
				"today at 05:30",
				Date::RenderDateExtended("1992-01-07 05:30:10", Lang::EN, true, DateFormatType::NICE, "1992-01-07 12:00:00")
			);

			$this->assertEquals(
				"اليوم في ٠٥:٣٠",
				Date::RenderDateExtended("1992-01-07 05:30:10", Lang::AR, true, DateFormatType::NICE, "1992-01-07 12:00:00")
			);

		}

		public function testGetDaysCountThrowError_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "date1"
			]));
			Date::GetDaysCount(null, "1992-01-07");
		}

		public function testGetDaysCountThrowError_02(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "date2"
			]));
			Date::GetDaysCount("1992-01-07", null);
		}

		public function testGetDaysCountSuccess(): void {
			$this->assertEquals(
				1,
				Date::GetDaysCount("1992-01-07", "1992-01-08")
			);

			$this->assertEquals(
				1.5,
				Date::GetDaysCount("1992-01-07 05:30:00", "1992-01-08 17:30:00")
			);

			$this->assertEquals(
				1.5,
				Date::GetDaysCount("1992-01-08 17:30:00", "1992-01-07 05:30:00")
			);
		}

		public function testGetAgeThrowError_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "dob"
			]));
			Date::GetAge(null);
		}

		public function testGetAgeSuccess(): void {
			$this->assertEquals(
				"30 years",
				Date::GetAge("1992-01-07", null, "2022-01-07")
			);

			$this->assertEquals(
				"30 years and 2 months",
				Date::GetAge("1992-01-07", null, "2022-03-07")
			);

			$this->assertEquals(
				"30 years and 2 months and 9 days",
				Date::GetAge("1992-01-07", null, "2022-03-16")
			);

			$this->assertEquals(
				"٣٠ سنوات و ٢ شهور و ٩ أيام",
				Date::GetAge("1992-01-07", Lang::AR, "2022-03-16")
			);

			$this->assertEquals(
				"30 years and 2 months",
				Date::GetAge("1992-01-07", null, "2022-03-16", true, false)
			);

			$this->assertEquals(
				"30 years and 9 days",
				Date::GetAge("1992-01-07", null, "2022-03-16", false, true)
			);

			$this->assertEquals(
				"30 years",
				Date::GetAge("1992-01-07", null, "2022-03-16", false, false)
			);
		}

		public function testGetFormatFromType(): void {
			$this->assertEquals(
				[
					DateType::DATE => DateFormat::DATE_SAVE,
					DateType::TIME => DateFormat::TIME_SAVE,
					DateType::DATETIME => DateFormat::DATETIME_SAVE,
				],
				Date::GetFormatFromType()
			);

			$this->assertEquals(
				[
					DateType::DATE => DateFormat::DATE_SAVE,
					DateType::TIME => DateFormat::TIME_MAIN,
					DateType::DATETIME => DateFormat::DATETIME_SAVE,
				],
				Date::GetFormatFromType(null, null, false)
			);

			$this->assertEquals(
				[
					DateType::DATE => DateFormat::DATE_MAIN,
					DateType::TIME => DateFormat::TIME_SAVE,
					DateType::DATETIME => DateFormat::DATETIME_MAIN,
				],
				Date::GetFormatFromType(DateFormatType::MAIN, null)
			);

			$this->assertEquals(
				[
					DateType::DATE => DateFormat::DATE_MAIN_NO_YEAR,
					DateType::TIME => DateFormat::TIME_MAIN,
					DateType::DATETIME => DateFormat::DATETIME_MAIN_NO_YEAR,
				],
				Date::GetFormatFromType(DateFormatType::MAIN, null, false)
			);

			$this->assertEquals(
				DateFormat::DATE_NICE,
				Date::GetFormatFromType(DateFormatType::NICE, DateType::DATE)
			);

			$this->assertEquals(
				DateFormat::DATETIME_NICE_NO_YEAR,
				Date::GetFormatFromType(DateFormatType::NICE, DateType::DATETIME, false)
			);
		}

	}
