<?php
	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Languages\LanguageTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Languages\Classes\Language;
	use RawadyMario\Languages\Models\Lang;

	final class LanguageTest extends TestCase {

		public function testSetVariableDefaultSuccess(): void {
			$this->assertEquals(
				Lang::EN,
				Language::$DEFAULT
			);

			Language::SetVariableDefault(Lang::AR);

			$this->assertEquals(
				Lang::AR,
				Language::$DEFAULT
			);

			Language::SetVariableDefault(Lang::EN);
		}

		public function testSetVariableActiveSuccess(): void {
			$this->assertEquals(
				Lang::EN,
				Language::$ACTIVE
			);

			Language::SetVariableActive(Lang::AR);

			$this->assertEquals(
				Lang::AR,
				Language::$ACTIVE
			);

			Language::SetVariableActive(Lang::EN);
		}

		public function testUppercaseSuccess(): void {
			$this->assertEquals(
				"MARIO RAWADY",
				Language::Uppercase("Mario Rawady")
			);

			$this->assertEquals(
				"MÀRIO RÂWÄDY",
				Language::Uppercase("Màrio Râwädy")
			);
		}

		public function testGetFieldKeySuccess(): void {
			$this->assertEquals(
				"first_name",
				Language::GetFieldKey("first_name")
			);

			$this->assertEquals(
				"first_name",
				Language::GetFieldKey("first_name", Lang::EN)
			);

			$this->assertEquals(
				"first_name_ar",
				Language::GetFieldKey("first_name", Lang::AR)
			);

			$this->assertEquals(
				"first_name_fr",
				Language::GetFieldKey("first_name", Lang::FR)
			);
		}

	}
