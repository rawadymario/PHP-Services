<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\HelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\Helper;
	
	final class HelperTest extends TestCase {
		
		public function testCleanStringSuccess(): void {
			$this->assertEquals(
				"Mario",
				Helper::CleanString(" Mario ")
			);
			
			$this->assertEquals(
				"Mario",
				Helper::CleanString(" Mario\\ ")
			);
			
			$this->assertEquals(
				"Mario\\",
				Helper::CleanString(" Mario\\\\ ")
			);
		}

		public function testCleanHtmlTextSuccess() {
			$this->assertEquals(
				"Mario",
				Helper::CleanHtmlText(" Mario ")
			);
			
			$this->assertEquals(
				"Mario&lt;br /&gt;",
				Helper::CleanHtmlText(" Mario<br /> ")
			);
		}

		public function testConvertToBoolSuccess() {
			//Type: String
			$this->assertTrue(
				Helper::ConvertToBool("Mario")
			);
			
			$this->assertFalse(
				Helper::ConvertToBool("false")
			);
			
			$this->assertFalse(
				Helper::ConvertToBool("")
			);

			//Type: Integer || Double
			$this->assertTrue(
				Helper::ConvertToBool(100)
			);
			
			$this->assertTrue(
				Helper::ConvertToBool(0.1)
			);
			
			$this->assertFalse(
				Helper::ConvertToBool(0)
			);
			
			//Type: Others
			$this->assertFalse(
				Helper::ConvertToBool(null)
			);
		}

		public function testConvertToIntSuccess() {
			$this->assertEquals(
				0,
				Helper::ConvertToInt("Mario")
			);

			$this->assertEquals(
				10,
				Helper::ConvertToInt("10")
			);
			
			$this->assertEquals(
				10,
				Helper::ConvertToInt(10)
			);
			
			$this->assertEquals(
				10,
				Helper::ConvertToInt(10.3)
			);
			
			$this->assertEquals(
				11,
				Helper::ConvertToInt(10.7)
			);
		}

		public function testConvertToDecSuccess() {
			$this->assertEquals(
				0,
				Helper::ConvertToDec("Mario")
			);

			$this->assertEquals(
				10,
				Helper::ConvertToDec("10")
			);

			$this->assertEquals(
				10.02,
				Helper::ConvertToDec("10.02")
			);

			$this->assertEquals(
				10.00,
				Helper::ConvertToDec("10.002")
			);

			$this->assertEquals(
				10.002,
				Helper::ConvertToDec("10.002", 3)
			);
		}

		public function testStringNullOrEmptySuccess() {
			$this->assertTrue(
				Helper::StringNullOrEmpty(null)
			);
			
			$this->assertTrue(
				Helper::StringNullOrEmpty("")
			);
			
			$this->assertFalse(
				Helper::StringNullOrEmpty(1)
			);
			
			$this->assertFalse(
				Helper::StringNullOrEmpty("Mario")
			);
		}

		public function testArrayNullOrEmptySuccess() {
			$this->assertTrue(
				Helper::ArrayNullOrEmpty(null)
			);
			
			$this->assertTrue(
				Helper::ArrayNullOrEmpty([])
			);
			
			$this->assertTrue(
				Helper::ArrayNullOrEmpty(json_decode(""))
			);
			
			$this->assertFalse(
				Helper::ArrayNullOrEmpty([
					"Mario"
				])
			);
			
			$this->assertFalse(
				Helper::ArrayNullOrEmpty(json_decode(json_encode([
					"Mario"
				])))
			);
		}

		public function testObjectNullOrEmptySuccess() {
			$this->assertTrue(
				Helper::ObjectNullOrEmpty(null)
			);
			
			$this->assertTrue(
				Helper::ObjectNullOrEmpty(json_decode(""))
			);
			
			$this->assertFalse(
				Helper::ObjectNullOrEmpty(json_decode("{}"))
			);
		}

		public function testEncryptPasswordSuccess() {
			$this->assertEquals(
				"db791c2671a6b3ff88259e6012ff78975cee69aaf3bf669f6fad35edb32a09489d35d880496eac67270203e15bd9a746ee720c8cba6f14c3631839e5d2e46e78",
				Helper::EncryptPassword("Mario")
			);
		}

		public function testGenerateRandomKeySuccess() {
			$key1En = Helper::GenerateRandomKey(8, false, false, false, "en");
			$key1Ar = Helper::GenerateRandomKey(8, false, false, false, "ar");
			
			$key2En = Helper::GenerateRandomKey(10, true, false, false, "en");
			$key2Ar = Helper::GenerateRandomKey(10, true, false, false, "ar");
			
			$key3En = Helper::GenerateRandomKey(12, true, true, false, "en");
			$key3Ar = Helper::GenerateRandomKey(12, true, true, false, "ar");
			
			$key4En = Helper::GenerateRandomKey(16, true, true, true, "en");
			$key4Ar = Helper::GenerateRandomKey(16, true, true, true, "ar");

			$this->assertEquals(0, strlen($key1En));
			$this->assertEquals(0, strlen($key1Ar));
			
			$this->assertEquals(10, strlen($key2En));
			$this->assertEquals(10, strlen($key2Ar));
			
			$this->assertEquals(12, strlen($key3En));
			$this->assertEquals(12, strlen($key3Ar));
			
			$this->assertEquals(16, strlen($key4En));
			$this->assertEquals(16, strlen($key4Ar));

			//TODO: Should add assertion for validating possible strings
		}

		public function testRemoveSlashesSuccess() {
			$this->assertEquals(
				"Mario",
				Helper::RemoveSlashes("\\Mario\\\\")
			);
			
			$this->assertEquals(
				"Mario",
				Helper::RemoveSlashes("\Mario\\")
			);
		}

		public function testRemoveSpacesSuccess() {
			$this->assertEquals(
				"MarioRawady",
				Helper::RemoveSpaces("  M a r i o R a w a d y ")
			);
		}

		public function testTruncateStrSuccess() {
			$this->assertEquals(
				"Mario Rawady",
				Helper::TruncateStr("Mario Rawady", 20)
			);
			
			$this->assertEquals(
				"Mario...",
				Helper::TruncateStr("Mario Rawady", 5)
			);
		}

		public function testStringBeginsWithSuccess() {
			$this->assertTrue(
				Helper::StringBeginsWith("Mario Rawady", "Mario")
			);
			
			$this->assertFalse(
				Helper::StringBeginsWith("Mario Rawady", "Rawady")
			);
		}

		public function testStringEndsWithSuccess() {
			$this->assertTrue(
				Helper::StringEndsWith("Mario Rawady", "Rawady")
			);
			
			$this->assertFalse(
				Helper::StringEndsWith("Mario Rawady", "Mario")
			);
		}

		public function testStringHasCharSuccess() {
			$this->assertTrue(
				Helper::StringHasChar("Mario Rawady", "Mario")
			);
			
			$this->assertTrue(
				Helper::StringHasChar("Mario Rawady", "Rawady")
			);
			
			$this->assertFalse(
				Helper::StringHasChar("Mario Rawady", "Marioss")
			);
		}

		public function testIsInStringSuccess() {
			$this->assertTrue(
				Helper::IsInString("Mario", "Mario Rawady")
			);
			
			$this->assertTrue(
				Helper::IsInString("Rawady", "Mario Rawady")
			);
			
			$this->assertFalse(
				Helper::IsInString("Marios", "Mario Rawady")
			);
		}

		public function testStripHtmlSuccess() {
			$this->assertEquals(
				"Mario",
				Helper::StripHtml("<h1>Mario</h1><br />")
			);
			
			$this->assertEquals(
				"<h1>Mario</h1>",
				Helper::StripHtml("<h1>Mario</h1><br />", "<h1>")
			);
			
			$this->assertEquals(
				"<h1>Mario</h1><br />",
				Helper::StripHtml("<h1>Mario</h1><br />", ["<h1>", "<br>"])
			);
		}

		public function testTextReplaceSuccess() {
			$this->assertEquals(
				"Mario Rawady",
				Helper::TextReplace("::FirstName:: ::LastName::", [
					"::FirstName::" => "Mario",
					"::LastName::" => "Rawady",
				])
			);
		}

		public function testSplitCamelcaseStringSuccess() {
			$this->assertEquals(
				"Mario Abdallah Rawady",
				Helper::SplitCamelcaseString("MarioAbdallahRawady")
			);
		}

		public function testGetStringSafeSuccess() {
			$this->assertEquals(
				"",
				Helper::GetStringSafe(null)
			);
			
			$this->assertEquals(
				"",
				Helper::GetStringSafe("")
			);
			
			$this->assertEquals(
				"1",
				Helper::GetStringSafe(1)
			);
			
			$this->assertEquals(
				"Mario",
				Helper::GetStringSafe("Mario")
			);
		}

		public function testGenerateClassNameFromStringSuccess() {
			$this->assertEquals(
				"MarioAbdallahRawady",
				Helper::GenerateClassNameFromString("mario-abdallah-rawady")
			);
			
			$this->assertEquals(
				"MarioAbdallahRawady",
				Helper::GenerateClassNameFromString("Mario Abdallah Rawady")
			);
		}

		public function testSafeNameSuccess() {
			$this->assertEquals(
				"marioabdallahrawady",
				Helper::SafeName("MarioAbdallahRawady")
			);
			
			$this->assertEquals(
				"mario-abdallah-rawady",
				Helper::SafeName("Mario Abdallah Rawady")
			);
			
			$this->assertEquals(
				"mario-abdallah-rawady",
				Helper::SafeName("Mario---Abdallah---Rawady")
			);
			
			$this->assertEquals(
				"mario-abdallah-rawady",
				Helper::SafeName("Mario@Abdallah!$%Rawady)({}")
			);
			
			$this->assertEquals(
				"ماريو-عبدالله-الروادي",
				Helper::SafeName("ماريو عبدالله الروادي!@#$%^&*()")
			);
		}

		public function testHasArabicCharSuccess() {
			$this->assertFalse(
				Helper::HasArabicChar("Mario Abdallah Rawady")
			);
			
			$this->assertFalse(
				Helper::HasArabicChar("!@#$%^&*()")
			);
			
			$this->assertFalse(
				Helper::HasArabicChar("1234567890")
			);
			
			$this->assertTrue(
				Helper::HasArabicChar("ماريو عبدالله الروادي")
			);
			
			$this->assertTrue(
				Helper::HasArabicChar("Mario Abdallah Rawady ماريو عبدالله الروادي")
			);
		}

		public function testExplodeStrToArrSuccess() {
			$this->assertEquals(
				[],
				Helper::ExplodeStrToArr(null)
			);
			
			$this->assertEquals(
				[],
				Helper::ExplodeStrToArr("")
			);
			
			$this->assertEquals(
				[
					"Mario Abdallah Rawady",
				],
				Helper::ExplodeStrToArr("Mario Abdallah Rawady")
			);
			
			$this->assertEquals(
				[
					"Ma",
					"ri",
					"o ",
					"Ab",
					"da",
					"ll",
					"ah",
					" R",
					"aw",
					"ad",
					"y",
				],
				Helper::ExplodeStrToArr("Mario Abdallah Rawady", "", 2)
			);
			
			$this->assertEquals(
				[
					"Mario",
					"Abdallah",
					"Rawady",
				],
				Helper::ExplodeStrToArr("Mario Abdallah Rawady", " ")
			);
			
			$this->assertEquals(
				[
					"M",
					"rio Abd",
					"ll",
					"h R",
					"w",
					"dy",
				],
				Helper::ExplodeStrToArr("Mario Abdallah Rawady", "a")
			);
		}

		public function testImplodeArrToStrSuccess() {
			$this->assertEquals(
				"",
				Helper::ImplodeArrToStr(null)
			);
			
			$this->assertEquals(
				"",
				Helper::ImplodeArrToStr([])
			);
			
			$this->assertEquals(
				"Mario Abdallah Rawady",
				Helper::ImplodeArrToStr([
					"Mario",
					"Abdallah",
					"Rawady"
				])
			);
			
			$this->assertEquals(
				"Mario Abdallah Rawady",
				Helper::ImplodeArrToStr([
					"Mario",
					"",
					"Abdallah",
					"",
					"Rawady"
				])
			);
		}

		public function testGetValueFromArrByKeySuccess() {
			$this->assertEquals(
				"",
				Helper::GetValueFromArrByKey(null)
			);
			
			$this->assertEquals(
				"",
				Helper::GetValueFromArrByKey([])
			);
			
			$this->assertEquals(
				"",
				Helper::GetValueFromArrByKey([
					"key1" => "Key 1",
				], "key2")
			);
			
			$this->assertEquals(
				"Key 2",
				Helper::GetValueFromArrByKey([
					"key1" => "Key 1",
					"key2" => "Key 2",
				], "key2")
			);
		}

		public function testUnsetArrayEmptyValuesSuccess() {
			$this->assertEquals(
				[],
				Helper::UnsetArrayEmptyValues(null)
			);
			
			$this->assertEquals(
				[],
				Helper::UnsetArrayEmptyValues([])
			);
			
			$this->assertEquals(
				[
					"Mario",
					"Abdallah",
					"Rawady"
				],
				Helper::UnsetArrayEmptyValues([
					"Mario",
					"Abdallah",
					"Rawady"
				])
			);
			
			$this->assertEquals(
				[
					"Mario",
					"Abdallah",
					"Rawady"
				],
				Helper::UnsetArrayEmptyValues([
					"Mario",
					"",
					"Abdallah",
					null,
					"Rawady"
				])
			);
		}

		public function testGererateKeyValueStringFromArraySuccess() {
			$this->assertEquals(
				"",
				Helper::GererateKeyValueStringFromArray(null)
			);
			
			$this->assertEquals(
				"",
				Helper::GererateKeyValueStringFromArray([])
			);
			
			$this->assertEquals(
				'type="text" name="test-input" id="test-input" placeholder="Test Input"',
				Helper::GererateKeyValueStringFromArray([
					"type" => "text",
					"name" => "test-input",
					"id" => "test-input",
					"placeholder" => "Test Input",
				])
			);
			
			$this->assertEquals(
				'pre_type="text" pre_name="test-input" pre_id="test-input" pre_placeholder="Test Input"',
				Helper::GererateKeyValueStringFromArray([
					"type" => "text",
					"name" => "test-input",
					"id" => "test-input",
					"placeholder" => "Test Input",
				], "pre_")
			);
			
			$this->assertEquals(
				'type:"text" name:"test-input" id:"test-input" placeholder:"Test Input"',
				Helper::GererateKeyValueStringFromArray([
					"type" => "text",
					"name" => "test-input",
					"id" => "test-input",
					"placeholder" => "Test Input",
				], "", ":")
			);
			
			$this->assertEquals(
				'type=-text- name=-test-input- id=-test-input- placeholder=-Test Input-',
				Helper::GererateKeyValueStringFromArray([
					"type" => "text",
					"name" => "test-input",
					"id" => "test-input",
					"placeholder" => "Test Input",
				], "", "=", "-")
			);
			
			$this->assertEquals(
				'type="text"_join_name="test-input"_join_id="test-input"_join_placeholder="Test Input"',
				Helper::GererateKeyValueStringFromArray([
					"type" => "text",
					"name" => "test-input",
					"id" => "test-input",
					"placeholder" => "Test Input",
				], "", "=", "\"", "_join_")
			);
		}

		public function testDirExistsSuccess() {
			$this->assertFalse(
				Helper::DirExists(null)
			);
			
			$this->assertFalse(
				Helper::DirExists("")
			);
			
			$this->assertFalse(
				Helper::DirExists("TestsForUnits", __DIR__ . "/")
			);
			
			$this->assertTrue(
				Helper::DirExists("Units", __DIR__ . "/../../")
			);
			
			$this->assertTrue(
				Helper::DirExists("TestsForUnits", __DIR__ . "/../../", true)
			);
		}

		
	}
	