<?php
	//To Run: .\vendor/bin/phpunit .\Units\Automated\Helpers\TranslateHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\NotEmptyParamException;
	use RawadyMario\Models\Lang;
	use RawadyMario\Helpers\TranslateHelper;

	final class TranslateHelperTest extends TestCase {

		public function setUp(): void {
			TranslateHelper::clear();
			parent::setUp();
		}

		public function testAddDefaultsSuccess(): void {
			$this->assertEmpty(
				TranslateHelper::GetTranlationsArray(),
				"Array should be Empty but it is not!"
			);

			TranslateHelper::AddDefaults();

			$this->assertNotEmpty(
				TranslateHelper::GetTranlationsArray(),
				"Array should not be Empty but it is!"
			);
		}

		public function testAddCustomDirSuccess(): void {
			$this->assertEmpty(
				TranslateHelper::GetTranlationsArray(),
				"Array should be Empty but it is not!"
			);

			TranslateHelper::AddCustomDir(__DIR__ . "/../../_TestsForUnits/CustomTranslations");

			$this->assertNotEmpty(
				TranslateHelper::GetTranlationsArray(),
				"Array should not be Empty but it is!"
			);
		}

		public function testTranslateThrowError_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(TranslateHelper::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "key"
			]));
			TranslateHelper::Translate(null);
		}

		public function testTranslateThrowError_02(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(TranslateHelper::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "key"
			]));
			TranslateHelper::Translate("");
		}

		public function testTranslateSuccess(): void {
			$this->assertEquals(
				"year",
				TranslateHelper::Translate("date.year")
			);

			$this->assertEquals(
				"سنة",
				TranslateHelper::Translate("date.year", Lang::AR)
			);

			$this->assertEquals(
				"سنة",
				TranslateHelper::Translate("year", Lang::AR, false, [], false)
			);

			$this->assertEquals(
				"date.yearss",
				TranslateHelper::Translate("date.yearss", Lang::EN, false)
			);

			$this->assertEquals(
				"",
				TranslateHelper::Translate("date.yearss", Lang::EN, true)
			);

			$this->assertEquals(
				"Mario",
				TranslateHelper::Translate("date.year", Lang::EN, false, [
					"year" => "Mario"
				])
			);

			$this->assertEquals(
				"Id1 = 1 - Id2 = 2",
				TranslateHelper::Translate("Id1 = {id_1} - Id2 = {id_2}", Lang::EN, false, [
					"{id_1}" => "1",
					"{id_2}" => "2",
				])
			);

			$this->assertEquals(
				"date.year",
				TranslateHelper::Translate("date.year", LANG::AR, false, [], false)
			);

			$this->assertEquals(
				"test.test1",
				TranslateHelper::Translate("test.test1")
			);

			TranslateHelper::AddCustomDir(__DIR__ . "/../../_TestsForUnits/CustomTranslations");

			$this->assertEquals(
				"Test 1",
				TranslateHelper::Translate("test.test1")
			);
		}

		public function testTranslateSimpleSuccess(): void {
			$this->assertEquals(
				"سنة",
				TranslateHelper::TranslateSimple("year", Lang::AR, false, [])
			);
		}

		public function testTranslateStringThrowError_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(TranslateHelper::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "string"
			]));
			TranslateHelper::TranslateString(null);
		}

		public function testTranslateStringThrowError_02(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(TranslateHelper::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "string"
			]));
			TranslateHelper::TranslateString("");
		}

		public function testTranslateStringSuccess(): void {
			$this->assertEquals(
				"This is the Year 2022",
				TranslateHelper::TranslateString("This is the date.Year 2022")
			);

			$this->assertEquals(
				"هذا هو سنة ٢٠٢٢",
				TranslateHelper::TranslateString("هذا هو date.Year number.2number.0number.2number.2", Lang::AR)
			);

			$this->assertEquals(
				"هذا هو سنة ٢٠٢٢",
				TranslateHelper::TranslateString("هذا هو Year 2022", Lang::AR, [], false)
			);
		}

		public function testTranslateStringSimpleSuccess(): void {
			$this->assertEquals(
				"١٩٩٢",
				TranslateHelper::TranslateStringSimple("1992", Lang::AR)
			);
		}

	}