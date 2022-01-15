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
		
	}
	