<?php
	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Microservices\Language\Helpers\TranslateTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\NotEmptyParamException;
	use RawadyMario\Language\Helpers\Translate;
	use RawadyMario\Language\Models\Lang;

	final class TranslateTest extends TestCase {

		public function setUp(): void {
			Translate::clear();
			parent::setUp();
		}

		public function testAddDefaultsSuccess(): void {
			$this->assertEmpty(
				Translate::GetTranlationsArray(),
				"Array should be Empty but it is not!"
			);

			Translate::AddDefaults();

			$this->assertNotEmpty(
				Translate::GetTranlationsArray(),
				"Array should not be Empty but it is!"
			);
		}

		public function testAddCustomDirSuccess(): void {
			$this->assertEmpty(
				Translate::GetTranlationsArray(),
				"Array should be Empty but it is not!"
			);

			Translate::AddCustomDir(__DIR__ . "/../../../_TestsForUnits/CustomTranslations");

			$this->assertNotEmpty(
				Translate::GetTranlationsArray(),
				"Array should not be Empty but it is!"
			);
		}

		public function testTranslateThrowError_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "key"
			]));
			Translate::Translate(null);
		}

		public function testTranslateThrowError_02(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "key"
			]));
			Translate::Translate("");
		}

		public function testTranslateSuccess(): void {
			$this->assertEquals(
				"year",
				Translate::Translate("date.year")
			);

			$this->assertEquals(
				"سنة",
				Translate::Translate("date.year", Lang::AR)
			);

			$this->assertEquals(
				"سنة",
				Translate::Translate("year", Lang::AR, false, [], false)
			);

			$this->assertEquals(
				"date.yearss",
				Translate::Translate("date.yearss", Lang::EN, false)
			);

			$this->assertEquals(
				"",
				Translate::Translate("date.yearss", Lang::EN, true)
			);

			$this->assertEquals(
				"Mario",
				Translate::Translate("date.year", Lang::EN, false, [
					"year" => "Mario"
				])
			);

			$this->assertEquals(
				"Id1 = 1 - Id2 = 2",
				Translate::Translate("Id1 = {id_1} - Id2 = {id_2}", Lang::EN, false, [
					"{id_1}" => "1",
					"{id_2}" => "2",
				])
			);

			$this->assertEquals(
				"date.year",
				Translate::Translate("date.year", Lang::AR, false, [], false)
			);

			$this->assertEquals(
				"test.test1",
				Translate::Translate("test.test1")
			);

			Translate::AddCustomDir(__DIR__ . "/../../../_TestsForUnits/CustomTranslations");

			$this->assertEquals(
				"Test 1",
				Translate::Translate("test.test1")
			);
		}

		public function testTranslateSimpleSuccess(): void {
			$this->assertEquals(
				"سنة",
				Translate::TranslateSimple("year", Lang::AR, false, [])
			);
		}

		public function testTranslateStringThrowError_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "string"
			]));
			Translate::TranslateString(null);
		}

		public function testTranslateStringThrowError_02(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "string"
			]));
			Translate::TranslateString("");
		}

		public function testTranslateStringSuccess(): void {
			$this->assertEquals(
				"This is the Year 2022",
				Translate::TranslateString("This is the date.Year 2022")
			);

			$this->assertEquals(
				"هذا هو سنة ٢٠٢٢",
				Translate::TranslateString("هذا هو date.Year number.2number.0number.2number.2", Lang::AR)
			);

			$this->assertEquals(
				"هذا هو سنة ٢٠٢٢",
				Translate::TranslateString("هذا هو Year 2022", Lang::AR, [], false)
			);
		}

		public function testTranslateStringSimpleSuccess(): void {
			$this->assertEquals(
				"١٩٩٢",
				Translate::TranslateStringSimple("1992", Lang::AR)
			);
		}

	}
