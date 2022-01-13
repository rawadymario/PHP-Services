<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\LangHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\LangHelper;

	final class LangHelperTest extends TestCase {
		
		public function testSetVariableDefaultSuccess(): void {
			$this->assertEquals(
				"en",
				LangHelper::$DEFAULT
			);

			LangHelper::SetVariableDefault("ar");
			
			$this->assertEquals(
				"ar",
				LangHelper::$DEFAULT
			);
			
			LangHelper::SetVariableDefault("en");
		}
		
		public function testSetVariableActiveSuccess(): void {
			$this->assertEquals(
				"en",
				LangHelper::$ACTIVE
			);

			LangHelper::SetVariableActive("ar");
			
			$this->assertEquals(
				"ar",
				LangHelper::$ACTIVE
			);
			
			LangHelper::SetVariableActive("en");
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
				LangHelper::GetFieldKey("first_name", "en")
			);
			
			$this->assertEquals(
				"first_name_ar",
				LangHelper::GetFieldKey("first_name", "ar")
			);
			
			$this->assertEquals(
				"first_name_fr",
				LangHelper::GetFieldKey("first_name", "fr")
			);
		}
		
	}
	