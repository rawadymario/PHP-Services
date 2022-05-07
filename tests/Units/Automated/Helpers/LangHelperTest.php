<?php
	//To Run: .\vendor/bin/phpunit .\Units\Automated\Helpers\LangHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Models\Lang;
	use RawadyMario\Helpers\LangHelper;

	final class LangHelperTest extends TestCase {

		public function testSetVariableDefaultSuccess(): void {
			$this->assertEquals(
				Lang::EN,
				LangHelper::$DEFAULT
			);

			LangHelper::SetVariableDefault(Lang::AR);

			$this->assertEquals(
				Lang::AR,
				LangHelper::$DEFAULT
			);

			LangHelper::SetVariableDefault(Lang::EN);
		}

		public function testSetVariableActiveSuccess(): void {
			$this->assertEquals(
				Lang::EN,
				LangHelper::$ACTIVE
			);

			LangHelper::SetVariableActive(Lang::AR);

			$this->assertEquals(
				Lang::AR,
				LangHelper::$ACTIVE
			);

			LangHelper::SetVariableActive(Lang::EN);
		}

		public function testUppercaseSuccess(): void {
			$this->assertEquals(
				"MARIO RAWADY",
				LangHelper::Uppercase("Mario Rawady")
			);

			$this->assertEquals(
				"MÀRIO RÂWÄDY",
				LangHelper::Uppercase("Màrio Râwädy")
			);
		}

		public function testGetFieldKeySuccess(): void {
			$this->assertEquals(
				"first_name",
				LangHelper::GetFieldKey("first_name")
			);

			$this->assertEquals(
				"first_name",
				LangHelper::GetFieldKey("first_name", Lang::EN)
			);

			$this->assertEquals(
				"first_name_ar",
				LangHelper::GetFieldKey("first_name", Lang::AR)
			);

			$this->assertEquals(
				"first_name_fr",
				LangHelper::GetFieldKey("first_name", Lang::FR)
			);
		}

	}
