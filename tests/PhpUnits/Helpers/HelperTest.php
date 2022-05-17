<?php
	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Helpers\HelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Exceptions\FileNotFoundException;
	use RawadyMario\Exceptions\NotEmptyParamException;
	use RawadyMario\Models\Code;
	use RawadyMario\Models\HttpCode;
	use RawadyMario\Language\Models\Lang;
	use RawadyMario\Models\Status;
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Language\Helpers\Translate;

	final class HelperTest extends TestCase {
		private const UPLOAD_DIR = __DIR__ . "/../../_CommonFiles/Upload/";

		public function test_clean_string_success(): void {
			$this->assertEquals(
				"Mario",
				Helper::clean_string(" Mario ")
			);

			$this->assertEquals(
				"Mario",
				Helper::clean_string(" Mario\\ ")
			);

			$this->assertEquals(
				"Mario\\",
				Helper::clean_string(" Mario\\\\ ")
			);
		}

		public function test_clean_html_text_success() {
			$this->assertEquals(
				"Mario",
				Helper::clean_html_text(" Mario ")
			);

			$this->assertEquals(
				"Mario&lt;br /&gt;",
				Helper::clean_html_text(" Mario<br /> ")
			);
		}

		public function test_convert_to_bool_success() {
			//Type: String
			$this->assertTrue(
				Helper::convert_to_bool("Mario")
			);

			$this->assertFalse(
				Helper::convert_to_bool("false")
			);

			$this->assertFalse(
				Helper::convert_to_bool("")
			);

			//Type: Integer || Double
			$this->assertTrue(
				Helper::convert_to_bool(100)
			);

			$this->assertTrue(
				Helper::convert_to_bool(0.1)
			);

			$this->assertFalse(
				Helper::convert_to_bool(0)
			);

			//Type: Boolean
			$this->assertTrue(
				Helper::convert_to_bool(true)
			);

			$this->assertFalse(
				Helper::convert_to_bool(false)
			);

			//Type: Others
			$this->assertFalse(
				Helper::convert_to_bool(null)
			);
		}

		public function test_convert_to_int_success() {
			$this->assertEquals(
				0,
				Helper::convert_to_int("Mario")
			);

			$this->assertEquals(
				10,
				Helper::convert_to_int("10")
			);

			$this->assertEquals(
				10,
				Helper::convert_to_int(10)
			);

			$this->assertEquals(
				10,
				Helper::convert_to_int(10.3)
			);

			$this->assertEquals(
				11,
				Helper::convert_to_int(10.7)
			);

			$this->assertEquals(
				-10,
				Helper::convert_to_int(-10.7)
			);
		}

		public function test_convert_to_dec_success() {
			$this->assertEquals(
				0,
				Helper::convert_to_dec("Mario")
			);

			$this->assertEquals(
				10,
				Helper::convert_to_dec("10")
			);

			$this->assertEquals(
				10.02,
				Helper::convert_to_dec("10.02")
			);

			$this->assertEquals(
				10.00,
				Helper::convert_to_dec("10.002")
			);

			$this->assertEquals(
				10.002,
				Helper::convert_to_dec("10.002", 3)
			);

			$this->assertEquals(
				-10.00,
				Helper::convert_to_dec("-10.002")
			);
		}

		public function test_convert_to_dec_as_string_success() {
			$this->assertEquals(
				"0",
				Helper::convert_to_dec_as_string("Mario")
			);

			$this->assertEquals(
				"0.0",
				Helper::convert_to_dec_as_string("Mario", 1)
			);

			$this->assertEquals(
				"10",
				Helper::convert_to_dec_as_string("10")
			);

			$this->assertEquals(
				"10.00",
				Helper::convert_to_dec_as_string("10", 2)
			);

			$this->assertEquals(
				"10.02",
				Helper::convert_to_dec_as_string("10.02", 2)
			);

			$this->assertEquals(
				"10.00",
				Helper::convert_to_dec_as_string("10.002", 2)
			);

			$this->assertEquals(
				"10.002",
				Helper::convert_to_dec_as_string("10.002", 3)
			);
		}

		public function test_string_null_or_empty_success() {
			$this->assertTrue(
				Helper::string_null_or_empty(null)
			);

			$this->assertTrue(
				Helper::string_null_or_empty("")
			);

			$this->assertFalse(
				Helper::string_null_or_empty(1)
			);

			$this->assertFalse(
				Helper::string_null_or_empty("Mario")
			);
		}

		public function test_array_null_or_empty_success() {
			$this->assertTrue(
				Helper::array_null_or_empty(null)
			);

			$this->assertTrue(
				Helper::array_null_or_empty([])
			);

			$this->assertTrue(
				Helper::array_null_or_empty(json_decode(""))
			);

			$this->assertFalse(
				Helper::array_null_or_empty([
					"Mario"
				])
			);

			$this->assertFalse(
				Helper::array_null_or_empty(json_decode(json_encode([
					"Mario"
				])))
			);
		}

		public function test_object_null_or_empty_success() {
			$this->assertTrue(
				Helper::object_null_or_empty(null)
			);

			$this->assertTrue(
				Helper::object_null_or_empty(json_decode(""))
			);

			$this->assertFalse(
				Helper::object_null_or_empty(json_decode("{}"))
			);
		}

		public function test_encrypt_password_success() {
			$this->assertEquals(
				"db791c2671a6b3ff88259e6012ff78975cee69aaf3bf669f6fad35edb32a09489d35d880496eac67270203e15bd9a746ee720c8cba6f14c3631839e5d2e46e78",
				Helper::encrypt_password("Mario")
			);
		}

		public function test_generate_random_key_success() {
			$key1En = Helper::generate_random_key(8, false, false, false, Lang::EN);
			$key1Ar = Helper::generate_random_key(8, false, false, false, Lang::AR);

			$key2En = Helper::generate_random_key(10, true, false, false, Lang::EN);
			$key2Ar = Helper::generate_random_key(10, true, false, false, Lang::AR);

			$key3En = Helper::generate_random_key(12, true, true, false, Lang::EN);
			$key3Ar = Helper::generate_random_key(12, true, true, false, Lang::AR);

			$key4En = Helper::generate_random_key(16, true, true, true, Lang::EN);
			$key4Ar = Helper::generate_random_key(16, true, true, true, Lang::AR);

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

		public function test_remove_slashes_success() {
			$this->assertEquals(
				"Mario",
				Helper::remove_slashes("\\Mario\\\\")
			);

			$this->assertEquals(
				"Mario",
				Helper::remove_slashes("\Mario\\")
			);
		}

		public function test_remove_spaces_success() {
			$this->assertEquals(
				"MarioRawady",
				Helper::remove_spaces("  M a r i o R a w a d y ")
			);
		}

		public function test_truncate_string_success() {
			$this->assertEquals(
				"Mario Rawady",
				Helper::truncate_string("Mario Rawady", 20)
			);

			$this->assertEquals(
				"Mario...",
				Helper::truncate_string("Mario Rawady", 5)
			);
		}

		public function test_string_begins_with_success() {
			$this->assertTrue(
				Helper::string_begins_with("Mario Rawady", "Mario")
			);

			$this->assertFalse(
				Helper::string_begins_with("Mario Rawady", "Rawady")
			);

			$this->assertTrue(
				Helper::string_begins_with("Mario Rawady", ["Mario"])
			);

			$this->assertTrue(
				Helper::string_begins_with("Mario Rawady", ["Mario", "Rawady"])
			);

			$this->assertTrue(
				Helper::string_begins_with("Mario Rawady", ["Maria", "Mario"])
			);

			$this->assertFalse(
				Helper::string_begins_with("Mario Rawady", ["Maria", "Rawady"])
			);

			$this->assertFalse(
				Helper::string_begins_with("Mario Rawady", ["Maria"])
			);
		}

		public function test_string_ends_with_success() {
			$this->assertTrue(
				Helper::string_ends_with("Mario Rawady", "Rawady")
			);

			$this->assertFalse(
				Helper::string_ends_with("Mario Rawady", "Mario")
			);

			$this->assertTrue(
				Helper::string_ends_with("Mario Rawady", ["Rawady"])
			);

			$this->assertTrue(
				Helper::string_ends_with("Mario Rawady", ["Mario", "Rawady"])
			);

			$this->assertTrue(
				Helper::string_ends_with("Mario Rawady", ["Rawody", "Rawady"])
			);

			$this->assertFalse(
				Helper::string_ends_with("Mario Rawady", ["Mario", "Rawody"])
			);

			$this->assertFalse(
				Helper::string_ends_with("Mario Rawady", ["Rawody"])
			);
		}

		public function test_string_has_char_success() {
			$this->assertTrue(
				Helper::string_has_char("Mario Rawady", "Mario")
			);

			$this->assertTrue(
				Helper::string_has_char("Mario Rawady", "Rawady")
			);

			$this->assertFalse(
				Helper::string_has_char("Mario Rawady", "Marioss")
			);
		}

		public function test_is_in_string_success() {
			$this->assertTrue(
				Helper::is_in_string("Mario", "Mario Rawady")
			);

			$this->assertTrue(
				Helper::is_in_string("Rawady", "Mario Rawady")
			);

			$this->assertFalse(
				Helper::is_in_string("Marios", "Mario Rawady")
			);
		}

		public function test_strip_html_success() {
			$this->assertEquals(
				"Mario",
				Helper::strip_html("<h1>Mario</h1><br />")
			);

			$this->assertEquals(
				"<h1>Mario</h1>",
				Helper::strip_html("<h1>Mario</h1><br />", "<h1>")
			);

			$this->assertEquals(
				"<h1>Mario</h1><br />",
				Helper::strip_html("<h1>Mario</h1><br />", ["<h1>", "<br>"])
			);
		}

		public function test_text_replace_success() {
			$this->assertEquals(
				"Mario Rawady",
				Helper::text_replace("::FirstName:: ::LastName::", [
					"::FirstName::" => "Mario",
					"::LastName::" => "Rawady",
				])
			);
		}

		public function test_split_camelcase_string_success() {
			$this->assertEquals(
				"Mario Abdallah Rawady",
				Helper::split_camelcase_string("MarioAbdallahRawady")
			);
		}

		public function test_get_string_safe_success() {
			$this->assertEquals(
				"",
				Helper::get_string_safe(null)
			);

			$this->assertEquals(
				"",
				Helper::get_string_safe("")
			);

			$this->assertEquals(
				"1",
				Helper::get_string_safe(1)
			);

			$this->assertEquals(
				"Mario",
				Helper::get_string_safe("Mario")
			);
		}

		public function test_generate_class_name_from_string_success() {
			$this->assertEquals(
				"MarioAbdallahRawady",
				Helper::generate_class_name_from_string("mario-abdallah-rawady")
			);

			$this->assertEquals(
				"MarioAbdallahRawady",
				Helper::generate_class_name_from_string("Mario Abdallah Rawady")
			);
		}

		public function test_safe_name_success() {
			$this->assertEquals(
				"marioabdallahrawady",
				Helper::safe_name("MarioAbdallahRawady")
			);

			$this->assertEquals(
				"mario-abdallah-rawady",
				Helper::safe_name("Mario Abdallah Rawady")
			);

			$this->assertEquals(
				"mario-abdallah-rawady",
				Helper::safe_name("Mario---Abdallah---Rawady")
			);

			$this->assertEquals(
				"mario-abdallah-rawady",
				Helper::safe_name("Mario@Abdallah!$%Rawady)({}")
			);

			$this->assertEquals(
				"ماريو-عبدالله-الروادي",
				Helper::safe_name("ماريو عبدالله الروادي!@#$%^&*()")
			);
		}

		public function test_has_arabic_char_success() {
			$this->assertFalse(
				Helper::has_arabic_char("Mario Abdallah Rawady")
			);

			$this->assertFalse(
				Helper::has_arabic_char("!@#$%^&*()")
			);

			$this->assertFalse(
				Helper::has_arabic_char("1234567890")
			);

			$this->assertTrue(
				Helper::has_arabic_char("ماريو عبدالله الروادي")
			);

			$this->assertTrue(
				Helper::has_arabic_char("Mario Abdallah Rawady ماريو عبدالله الروادي")
			);
		}

		public function test_explode_str_to_arr_success() {
			$this->assertEquals(
				[],
				Helper::explode_str_to_arr(null)
			);

			$this->assertEquals(
				[],
				Helper::explode_str_to_arr("")
			);

			$this->assertEquals(
				[
					"Mario Abdallah Rawady",
				],
				Helper::explode_str_to_arr("Mario Abdallah Rawady")
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
				Helper::explode_str_to_arr("Mario Abdallah Rawady", "", 2)
			);

			$this->assertEquals(
				[
					"Mario",
					"Abdallah",
					"Rawady",
				],
				Helper::explode_str_to_arr("Mario Abdallah Rawady", " ")
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
				Helper::explode_str_to_arr("Mario Abdallah Rawady", "a")
			);
		}

		public function test_implode_arr_to_str_success() {
			$this->assertEquals(
				"",
				Helper::implode_arr_to_str(null)
			);

			$this->assertEquals(
				"",
				Helper::implode_arr_to_str([])
			);

			$this->assertEquals(
				"Mario Abdallah Rawady",
				Helper::implode_arr_to_str([
					"Mario",
					"Abdallah",
					"Rawady"
				])
			);

			$this->assertEquals(
				"Mario Abdallah Rawady",
				Helper::implode_arr_to_str([
					"Mario",
					"",
					"Abdallah",
					"",
					"Rawady"
				])
			);
		}

		public function test_get_value_from_arr_by_key_success() {
			$this->assertEquals(
				"",
				Helper::get_value_from_arr_by_key(null)
			);

			$this->assertEquals(
				"",
				Helper::get_value_from_arr_by_key([])
			);

			$this->assertEquals(
				"",
				Helper::get_value_from_arr_by_key([
					"key1" => "Key 1",
				], "key2")
			);

			$this->assertEquals(
				"Key 2",
				Helper::get_value_from_arr_by_key([
					"key1" => "Key 1",
					"key2" => "Key 2",
				], "key2")
			);
		}

		public function test_unset_array_empty_values_success() {
			$this->assertEquals(
				[],
				Helper::unset_array_empty_values(null)
			);

			$this->assertEquals(
				[],
				Helper::unset_array_empty_values([])
			);

			$this->assertEquals(
				[
					"Mario",
					"Abdallah",
					"Rawady"
				],
				Helper::unset_array_empty_values([
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
				Helper::unset_array_empty_values([
					"Mario",
					"",
					"Abdallah",
					null,
					"Rawady"
				])
			);
		}

		public function test_gererate_key_value_string_from_array_success() {
			$this->assertEquals(
				"",
				Helper::gererate_key_value_string_from_array(null)
			);

			$this->assertEquals(
				"",
				Helper::gererate_key_value_string_from_array([])
			);

			$this->assertEquals(
				'type="text" name="test-input" id="test-input" placeholder="Test Input"',
				Helper::gererate_key_value_string_from_array([
					"type" => "text",
					"name" => "test-input",
					"id" => "test-input",
					"placeholder" => "Test Input",
				])
			);

			$this->assertEquals(
				'pre_type="text" pre_name="test-input" pre_id="test-input" pre_placeholder="Test Input"',
				Helper::gererate_key_value_string_from_array([
					"type" => "text",
					"name" => "test-input",
					"id" => "test-input",
					"placeholder" => "Test Input",
				], "pre_")
			);

			$this->assertEquals(
				'type:"text" name:"test-input" id:"test-input" placeholder:"Test Input"',
				Helper::gererate_key_value_string_from_array([
					"type" => "text",
					"name" => "test-input",
					"id" => "test-input",
					"placeholder" => "Test Input",
				], "", ":")
			);

			$this->assertEquals(
				'type=-text- name=-test-input- id=-test-input- placeholder=-Test Input-',
				Helper::gererate_key_value_string_from_array([
					"type" => "text",
					"name" => "test-input",
					"id" => "test-input",
					"placeholder" => "Test Input",
				], "", "=", "-")
			);

			$this->assertEquals(
				'type="text"_join_name="test-input"_join_id="test-input"_join_placeholder="Test Input"',
				Helper::gererate_key_value_string_from_array([
					"type" => "text",
					"name" => "test-input",
					"id" => "test-input",
					"placeholder" => "Test Input",
				], "", "=", "\"", "_join_")
			);
		}

		public function test_directory_exists_success() {
			$this->assertFalse(
				Helper::directory_exists(null)
			);

			$this->assertFalse(
				Helper::directory_exists("")
			);

			$this->assertFalse(
				Helper::directory_exists("_CommonFiles", __DIR__ . "/")
			);

			$this->assertTrue(
				Helper::directory_exists("PhpUnits", __DIR__ . "/../../")
			);

			$this->assertFalse(
				Helper::directory_exists("_CommonFiles", __DIR__ . "/../../../")
			);

			$this->assertTrue(
				Helper::directory_exists("_CommonFiles", __DIR__ . "/../../../", true)
			);
		}

		public function test_create_folder_success(): void {
			$this->assertFalse(
				Helper::directory_exists("NewFolder", self::UPLOAD_DIR)
			);
			$this->assertTrue(
				Helper::create_folder(self::UPLOAD_DIR . "NewFolder")
			);
		}

		public function test_create_folder_fail(): void {
			$this->assertTrue(
				Helper::directory_exists("NewFolder", self::UPLOAD_DIR)
			);
			$this->assertFalse(
				Helper::create_folder(self::UPLOAD_DIR . "NewFolder")
			);
		}

		public function test_delete_folder_success(): void {
			$this->assertTrue(
				Helper::directory_exists("NewFolder", self::UPLOAD_DIR)
			);
			$this->assertTrue(
				Helper::delete_file_or_folder(self::UPLOAD_DIR . "NewFolder")
			);
		}

		public function test_delete_file_success(): void {
			$newFile = self::UPLOAD_DIR . "unit-test.txt";

			$this->assertFalse(
				file_exists($newFile)
			);
			copy(self::UPLOAD_DIR . "test.txt", $newFile);
			$this->assertTrue(
				file_exists($newFile)
			);

			$this->assertTrue(
				Helper::delete_file_or_folder($newFile)
			);
		}

		public function test_get_youtube_id_success() {
			$this->assertEquals(
				"",
				Helper::get_youtube_id(null)
			);

			$this->assertEquals(
				"",
				Helper::get_youtube_id("")
			);

			$this->assertEquals(
				"IZbN_nmxAGk",
				Helper::get_youtube_id("https://www.youtube.com/embed/IZbN_nmxAGk")
			);
		}

		public function test_encrypt_string_success() {
			$this->assertEquals(
				"",
				Helper::encrypt_string(null)
			);

			$this->assertEquals(
				"",
				Helper::encrypt_string("")
			);

			$this->assertEquals(
				str_replace("&", "[amp;]", base64_encode("https://rawadymario.com/projects?page=2&category=2")),
				Helper::encrypt_string("https://rawadymario.com/projects?page=2&category=2")
			);
		}

		public function test_decrypt_string_success() {
			$this->assertEquals(
				"",
				Helper::decrypt_string(null)
			);

			$this->assertEquals(
				"",
				Helper::decrypt_string("")
			);

			$this->assertEquals(
				"https://rawadymario.com/projects?page=2&category=2",
				Helper::decrypt_string(str_replace("&", "[amp;]", base64_encode("https://rawadymario.com/projects?page=2&category=2")))
			);
		}

		public function get_status_class_from_codeProvider() {
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
		 * @dataProvider get_status_class_from_codeProvider
		 *
		 * @param $givenCode int
		 * @param $expectedStatus string
		 */
		public function test_get_status_class_from_code_success(int $givenCode, string $expectedStatus) {
			$this->assertEquals(
				$expectedStatus,
				Helper::get_status_class_from_code($givenCode)
			);
		}

		public function test_get_html_content_from_file_throw_error_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "file_path"
			]));
			Helper::get_html_content_from_file(null);
		}

		public function test_get_html_content_from_file_throw_error_02(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "file_path"
			]));
			Helper::get_html_content_from_file("");
		}

		public function test_get_html_content_from_file_throw_error_03(): void {
			$this->expectException(FileNotFoundException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.FileNotFound", null, [
				"::params::" => "file_path"
			]));
			Helper::get_html_content_from_file(__DIR__ . "/../../_CommonFiles/randomfile.html");
		}

		public function test_get_html_content_from_file_without_replace_success() {
			$this->assertEquals(
				"<h1>testGetHtmlContentFromFileWithoutReplaceSuccess</h1>",
				Helper::get_html_content_from_file(__DIR__ . "/../../_CommonFiles/testGetHtmlContentFromFileWithoutReplaceSuccess.html")
			);
		}

		public function test_get_html_content_from_file_with_replace_success() {
			$this->assertEquals(
				"<h1>testGetHtmlContentFromFileWithReplaceSuccess</h1>\n<h2>Replaced Text 01</h2>\n<h3>Replaced Text 02</h3>",
				Helper::get_html_content_from_file(__DIR__ . "/../../_CommonFiles/testGetHtmlContentFromFileWithReplaceSuccess.html", [
					"::replace_1::" => "Replaced Text 01",
					"::replace_2::" => "Replaced Text 02",
				])
			);
		}

		public function test_get_json_content_from_file_as_array_throw_error_01(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "file_path"
			]));
			Helper::get_json_content_from_file_as_array(null);
		}

		public function test_get_json_content_from_file_as_array_throw_error_02(): void {
			$this->expectException(NotEmptyParamException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.NotEmptyParam", null, [
				"::params::" => "file_path"
			]));
			Helper::get_json_content_from_file_as_array("");
		}

		public function test_get_json_content_from_file_as_array_throw_error_03(): void {
			$this->expectException(FileNotFoundException::class);
			$this->expectExceptionMessage(Translate::TranslateString("exception.FileNotFound", null, [
				"::params::" => "file_path"
			]));
			Helper::get_json_content_from_file_as_array(__DIR__ . "/../../_CommonFiles/randomfile.json");
		}

		public function test_get_json_content_from_file_as_array_success() {
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
				Helper::get_json_content_from_file_as_array(__DIR__ . "/../../_CommonFiles/testGetJsonContentFromFileAsArraySuccess.json")
			);
		}

		public function test_generate_full_url_success() {
			$this->assertEquals(
				"home",
				Helper::generate_full_url("home")
			);

			$this->assertEquals(
				"home/en",
				Helper::generate_full_url("home", Lang::EN)
			);

			$this->assertEquals(
				"home?lang=en",
				Helper::generate_full_url("home", Lang::EN, [], [], "", false)
			);

			$this->assertEquals(
				"products/en/product-001",
				Helper::generate_full_url("products", Lang::EN, [
					"key" => "product-001"
				])
			);

			$this->assertEquals(
				"products?lang=en&key=product-001",
				Helper::generate_full_url("products", Lang::EN, [
					"key" => "product-001"
				], [], "", false)
			);

			$this->assertEquals(
				"products/en/product-001?filter=active",
				Helper::generate_full_url("products", Lang::EN, [
					"key" => "product-001"
				], [
					"filter" => "active"
				])
			);

			$this->assertEquals(
				"products?lang=en&key=product-001&filter=active",
				Helper::generate_full_url("products", Lang::EN, [
					"key" => "product-001"
				], [
					"filter" => "active"
				], "", false)
			);

			$this->assertEquals(
				"products/en/product-001?filter=active&categories%5B%5D=category-01&categories%5B%5D=category-02&categories%5B%5D=category-03",
				Helper::generate_full_url("products", Lang::EN, [
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
				Helper::generate_full_url("home", Lang::EN, [], [], "https://rawadymario.com/")
			);

			$this->assertEquals(
				"https://rawadymario.com/home/en",
				Helper::generate_full_url("home", Lang::EN, [], [], "https://rawadymario.com")
			);

			$this->assertEquals(
				"https://rawadymario.com/home/en",
				Helper::generate_full_url("home", Lang::EN, [], [], "https://rawadymario.com////")
			);

			$this->assertEquals(
				"www.rawadymario.com/home/en",
				Helper::generate_full_url("home", Lang::EN, [], [], "www.rawadymario.com////")
			);
		}

		public function test_add_version_parameter_to_path_success() {
			$this->assertEquals(
				"assets/css/styles.css",
				Helper::add_version_parameter_to_path("assets/css/styles.css", "")
			);

			$this->assertEquals(
				"https://rawadymario.com/assets/css/styles.css",
				Helper::add_version_parameter_to_path("assets/css/styles.css", "https://rawadymario.com")
			);

			$this->assertEquals(
				"https://rawadymario.com/assets/css/styles.css?v=1.0",
				Helper::add_version_parameter_to_path("assets/css/styles.css", "https://rawadymario.com", "1.0")
			);
		}

		public function test_get_all_files_success() {
			$dir = str_replace("\PhpUnits\Helpers", "\_CommonFiles\Recursive", __DIR__);

			$this->assertEqualsCanonicalizing(
				[
					$dir . "/file1.html",
					$dir . "/file2.html",
				],
				Helper::get_all_files($dir, false)
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
				Helper::get_all_files($dir, true)
			);
		}

		public function test_convert_multidimention_array_to_single_dimention_success() {
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
			], Helper::convert_multidimention_array_to_single_dimention([
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

		public function test_add_scheme_if_missing_success() {
			$this->assertEquals(
				"",
				Helper::add_scheme_if_missing("", "")
			);

			$this->assertEquals(
				"rawadymario.com",
				Helper::add_scheme_if_missing("rawadymario.com", "")
			);

			$this->assertEquals(
				"http://rawadymario.com",
				Helper::add_scheme_if_missing("http://rawadymario.com", "https://")
			);

			$this->assertEquals(
				"https://rawadymario.com",
				Helper::add_scheme_if_missing("https://rawadymario.com", "http://")
			);

			$this->assertEquals(
				"https://rawadymario.com",
				Helper::add_scheme_if_missing("rawadymario.com", "https")
			);

			$this->assertEquals(
				"https://rawadymario.com",
				Helper::add_scheme_if_missing("rawadymario.com", "https://")
			);
		}

		public function test_replace_scheme_success() {
			$this->assertEquals(
				"",
				Helper::replace_scheme("", "")
			);

			$this->assertEquals(
				"rawadymario.com",
				Helper::replace_scheme("rawadymario.com", "")
			);

			$this->assertEquals(
				"https://rawadymario.com",
				Helper::replace_scheme("http://rawadymario.com", "https://")
			);

			$this->assertEquals(
				"http://rawadymario.com",
				Helper::replace_scheme("https://rawadymario.com", "http://")
			);

			$this->assertEquals(
				"https://rawadymario.com",
				Helper::replace_scheme("rawadymario.com", "https")
			);

			$this->assertEquals(
				"https://rawadymario.com",
				Helper::replace_scheme("rawadymario.com", "https://")
			);
		}

		public function test_is_valid_url() {
			$this->assertFalse(
				Helper::is_valid_url("Mario Rawady")
			);

			$this->assertFalse(
				Helper::is_valid_url("Mario Rawady: https://rawadymario.com")
			);

			$this->assertFalse(
				Helper::is_valid_url("http//rawadymario.com")
			);

			$this->assertFalse(
				Helper::is_valid_url("http:/rawadymario.com")
			);

			$this->assertFalse(
				Helper::is_valid_url("http:rawadymario.com")
			);

			$this->assertFalse(
				Helper::is_valid_url("https:/rawadymario.com")
			);

			$this->assertTrue( //To be fixed!
				Helper::is_valid_url("http://rawadymario.com Mario Rawady")
			);

			$this->assertTrue(
				Helper::is_valid_url("http://rawadymario.com")
			);

			$this->assertTrue(
				Helper::is_valid_url("https://rawadymario.com")
			);
		}

	}
