<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\HelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Constants\Code;
	use RawadyMario\Constants\HttpCode;
	use RawadyMario\Constants\Lang;
	use RawadyMario\Constants\Status;
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

		public function testConvertToDecAsStringSuccess() {
			$this->assertEquals(
				"0",
				Helper::ConvertToDecAsString("Mario")
			);

			$this->assertEquals(
				"0.0",
				Helper::ConvertToDecAsString("Mario", 1)
			);

			$this->assertEquals(
				"10",
				Helper::ConvertToDecAsString("10")
			);

			$this->assertEquals(
				"10.00",
				Helper::ConvertToDecAsString("10", 2)
			);

			$this->assertEquals(
				"10.02",
				Helper::ConvertToDecAsString("10.02", 2)
			);

			$this->assertEquals(
				"10.00",
				Helper::ConvertToDecAsString("10.002", 2)
			);

			$this->assertEquals(
				"10.002",
				Helper::ConvertToDecAsString("10.002", 3)
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
			$key1En = Helper::GenerateRandomKey(8, false, false, false, Lang::EN);
			$key1Ar = Helper::GenerateRandomKey(8, false, false, false, Lang::AR);

			$key2En = Helper::GenerateRandomKey(10, true, false, false, Lang::EN);
			$key2Ar = Helper::GenerateRandomKey(10, true, false, false, Lang::AR);

			$key3En = Helper::GenerateRandomKey(12, true, true, false, Lang::EN);
			$key3Ar = Helper::GenerateRandomKey(12, true, true, false, Lang::AR);

			$key4En = Helper::GenerateRandomKey(16, true, true, true, Lang::EN);
			$key4Ar = Helper::GenerateRandomKey(16, true, true, true, Lang::AR);

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
				Helper::DirExists("_TestsForUnits", __DIR__ . "/")
			);

			$this->assertTrue(
				Helper::DirExists("Units", __DIR__ . "/../../")
			);

			$this->assertTrue(
				Helper::DirExists("_TestsForUnits", __DIR__ . "/../../", true)
			);
		}

		public function testGetYoutubeIdSuccess() {
			$this->assertEquals(
				"",
				Helper::GetYoutubeId(null)
			);

			$this->assertEquals(
				"",
				Helper::GetYoutubeId("")
			);

			$this->assertEquals(
				"IZbN_nmxAGk",
				Helper::GetYoutubeId("https://www.youtube.com/embed/IZbN_nmxAGk")
			);
		}

		public function testEncryptLinkSuccess() {
			$this->assertEquals(
				"",
				Helper::EncryptLink(null)
			);

			$this->assertEquals(
				"",
				Helper::EncryptLink("")
			);

			$this->assertEquals(
				str_replace("&", "[amp;]", base64_encode("https://rawadymario.com/projects?page=2&category=2")),
				Helper::EncryptLink("https://rawadymario.com/projects?page=2&category=2")
			);
		}

		public function testDecryptLinkSuccess() {
			$this->assertEquals(
				"",
				Helper::DecryptLink(null)
			);

			$this->assertEquals(
				"",
				Helper::DecryptLink("")
			);

			$this->assertEquals(
				"https://rawadymario.com/projects?page=2&category=2",
				Helper::DecryptLink(str_replace("&", "[amp;]", base64_encode("https://rawadymario.com/projects?page=2&category=2")))
			);
		}

		public function GetStatusClassFromCodeProvider() {
			return [
				[Code::SUCCESS, Status::SUCCESS],
				[HttpCode::OK, Status::SUCCESS],
				[HttpCode::CREATED, Status::SUCCESS],
				[HttpCode::ACCEPTED, Status::SUCCESS],

				[Code::ERROR, Status::ERROR],
				[HttpCode::BADREQUEST, Status::ERROR],
				[HttpCode::UNAUTHORIZED, Status::ERROR],
				[HttpCode::FORBIDDEN, Status::ERROR],
				[HttpCode::NOTFOUND, Status::ERROR],
				[HttpCode::NOTALLOWED, Status::ERROR],
				[HttpCode::INTERNALERROR, Status::ERROR],
				[HttpCode::UNAVAILABLE, Status::ERROR],

				[Code::WARNING, Status::WARNING],

				[Code::INFO, Status::INFO],
				[Code::COMMON_INFO, Status::INFO],
				[HttpCode::CONTINUE, Status::INFO],
				[HttpCode::PROCESSING, Status::INFO],
			];
		}

		/**
		 * @dataProvider GetStatusClassFromCodeProvider
		 *
		 * @param $givenCode int
		 * @param $expectedStatus string
		 */
		public function testGetStatusClassFromCodeSuccess(int $givenCode, string $expectedStatus) {
			$this->assertEquals(
				$expectedStatus,
				Helper::GetStatusClassFromCode($givenCode)
			);
		}

		public function testGetHtmlContentFromFileSuccess() {
			$this->assertEquals(
				"",
				Helper::GetHtmlContentFromFile(null)
			);

			$this->assertEquals(
				"",
				Helper::GetHtmlContentFromFile("")
			);

			$this->assertEquals(
				"",
				Helper::GetHtmlContentFromFile(__DIR__ . "/../_TestsForUnits/randomfile.html")
			);

			$this->assertEquals(
				"<h1>testGetHtmlContentFromFileSuccess</h1>",
				Helper::GetHtmlContentFromFile(__DIR__ . "/../_TestsForUnits/testGetHtmlContentFromFileSuccess.html")
			);
		}

		public function testGetJsonContentFromFileAsArraySuccess() {
			$this->assertEquals(
				[],
				Helper::GetJsonContentFromFileAsArray(null)
			);

			$this->assertEquals(
				[],
				Helper::GetJsonContentFromFileAsArray("")
			);

			$this->assertEquals(
				[],
				Helper::GetJsonContentFromFileAsArray(__DIR__ . "/../_TestsForUnits/randomfile.json")
			);

			$this->assertEquals(
				[
					"fullName" => [
						"firstName" => "Mario",
						"middleName" => "Abdallah",
						"lastName" => "Rawady",
					],
					"position" => "Senior Software Engineer",
					"languages" => [
						"Arabic",
						"English",
						"French",
						"Spanish",
					]
				],
				Helper::GetJsonContentFromFileAsArray(__DIR__ . "/../_TestsForUnits/testGetJsonContentFromFileAsArraySuccess.json")
			);
		}

		public function testGenerateFullUrlSuccess() {
			$this->assertEquals(
				"home",
				Helper::GenerateFullUrl("home")
			);

			$this->assertEquals(
				"home/en",
				Helper::GenerateFullUrl("home", Lang::EN)
			);

			$this->assertEquals(
				"home?lang=en",
				Helper::GenerateFullUrl("home", Lang::EN, [], [], "", false)
			);

			$this->assertEquals(
				"products/en/product-001",
				Helper::GenerateFullUrl("products", Lang::EN, [
					"key" => "product-001"
				])
			);

			$this->assertEquals(
				"products?lang=en&key=product-001",
				Helper::GenerateFullUrl("products", Lang::EN, [
					"key" => "product-001"
				], [], "", false)
			);

			$this->assertEquals(
				"products/en/product-001?filter=active",
				Helper::GenerateFullUrl("products", Lang::EN, [
					"key" => "product-001"
				], [
					"filter" => "active"
				])
			);

			$this->assertEquals(
				"products?lang=en&key=product-001&filter=active",
				Helper::GenerateFullUrl("products", Lang::EN, [
					"key" => "product-001"
				], [
					"filter" => "active"
				], "", false)
			);

			$this->assertEquals(
				"products/en/product-001?filter=active&categories%5B%5D=category-01&categories%5B%5D=category-02&categories%5B%5D=category-03",
				Helper::GenerateFullUrl("products", Lang::EN, [
					"key" => "product-001"
				], [
					"filter" => "active",
					"categories" => [
						"category-01",
						"category-02",
						"category-03",
					]
				])
			);

			$this->assertEquals(
				"https://rawadymario.com/home/en",
				Helper::GenerateFullUrl("home", Lang::EN, [], [], "https://rawadymario.com/")
			);

			$this->assertEquals(
				"https://rawadymario.com/home/en",
				Helper::GenerateFullUrl("home", Lang::EN, [], [], "https://rawadymario.com")
			);

			$this->assertEquals(
				"https://rawadymario.com/home/en",
				Helper::GenerateFullUrl("home", Lang::EN, [], [], "https://rawadymario.com////")
			);

			$this->assertEquals(
				"www.rawadymario.com/home/en",
				Helper::GenerateFullUrl("home", Lang::EN, [], [], "www.rawadymario.com////")
			);
		}

		public function testAddVersionParameterToPathSuccess() {
			$this->assertEquals(
				"assets/css/styles.css",
				Helper::AddVersionParameterToPath("assets/css/styles.css", "")
			);

			$this->assertEquals(
				"https://rawadymario.com/assets/css/styles.css",
				Helper::AddVersionParameterToPath("assets/css/styles.css", "https://rawadymario.com")
			);

			$this->assertEquals(
				"https://rawadymario.com/assets/css/styles.css?v=1.0",
				Helper::AddVersionParameterToPath("assets/css/styles.css", "https://rawadymario.com", "1.0")
			);
		}

		public function testGetAllFilesSuccess() {
			$dir = str_replace("\Helpers", "\_TestsForUnits\Recursive", __DIR__);

			$this->assertEqualsCanonicalizing(
				[
					$dir . "/file1.html",
					$dir . "/file2.html",
				],
				Helper::GetAllFiles($dir, false)
			);

			$this->assertEqualsCanonicalizing(
				[
					$dir . "/file1.html",
					$dir . "/file2.html",
					$dir . "/Folder1/file1.html",
					$dir . "/Folder1/file2.html",
					$dir . "/Folder2/file1.html",
					$dir . "/Folder2/file2.html",
				],
				Helper::GetAllFiles($dir, true)
			);
		}

		public function testConvertMultidimentionArrayToSingleDimentionSuccess() {
			$this->assertEquals([
				"name.first" => "Mario",
				"name.middle" => "Abdallah",
				"name.last" => "Rawady",
				"address.building" => "Bldg",
				"address.street" => "Street",
				"address.region" => "Region",
				"address.country" => "Lebanon",
				"contact.info.mobile" => "+961111111",
				"contact.info.email" => "email@test.com",
			], Helper::ConvertMultidimentionArrayToSingleDimention([
				"name" => [
					"first" => "Mario",
					"middle" => "Abdallah",
					"last" => "Rawady",
				],
				"address" => [
					"building" => "Bldg",
					"street" => "Street",
					"region" => "Region",
					"country" => "Lebanon"
				],
				"contact" => [
					"info" => [
						"mobile" => "+961111111",
						"email" => "email@test.com"
					]
				]
			]));
		}

	}
