<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\TranslateHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\TranslateHelper;

	final class TranslateHelperTest extends TestCase {

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

		public function testTranslateSuccess(): void {
			$this->assertEquals(
				"",
				TranslateHelper::Translate(null)
			);

			$this->assertEquals(
				"",
				TranslateHelper::Translate("")
			);

			$this->assertEquals(
				"year",
				TranslateHelper::Translate("date.year")
			);

			$this->assertEquals(
				"سنة",
				TranslateHelper::Translate("date.year", "ar")
			);

			$this->assertEquals(
				"سنة",
				TranslateHelper::Translate("year", "ar", false, [], false)
			);

			$this->assertEquals(
				"date.yearss",
				TranslateHelper::Translate("date.yearss", "en", false)
			);

			$this->assertEquals(
				"",
				TranslateHelper::Translate("date.yearss", "en", true)
			);

			$this->assertEquals(
				"Mario",
				TranslateHelper::Translate("date.year", "en", false, [
					"year" => "Mario"
				])
			);
		}

		public function testTranslateStringSuccess(): void {
			$this->assertEquals(
				"",
				TranslateHelper::TranslateString(null)
			);

			$this->assertEquals(
				"",
				TranslateHelper::TranslateString("")
			);

			$this->assertEquals(
				"This is the Year 2022",
				TranslateHelper::TranslateString("This is the date.Year 2022")
			);

			$this->assertEquals(
				"هذا هو سنة ٢٠٢٢",
				TranslateHelper::TranslateString("هذا هو date.Year number.2number.0number.2number.2", "ar")
			);

			$this->assertEquals(
				"هذا هو سنة ٢٠٢٢",
				TranslateHelper::TranslateString("هذا هو Year 2022", "ar", [], false)
			);
		}

	}
