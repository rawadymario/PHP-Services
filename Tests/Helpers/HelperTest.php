<?php
	//To Run: .\vendor/bin/phpunit .\Tests\Helpers\HelperTest.php
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
			$this->assertEquals(
				true,
				Helper::ConvertToBool("Mario")
			);
			
			$this->assertEquals(
				false,
				Helper::ConvertToBool("false")
			);
			
			$this->assertEquals(
				false,
				Helper::ConvertToBool("")
			);

			//Type: Integer || Double
			$this->assertEquals(
				true,
				Helper::ConvertToBool(100)
			);
			
			$this->assertEquals(
				true,
				Helper::ConvertToBool(0.1)
			);
			
			$this->assertEquals(
				false,
				Helper::ConvertToBool(0)
			);
			
			//Type: Others
			$this->assertEquals(
				false,
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
			$this->assertEquals(
				true,
				Helper::StringNullOrEmpty(null)
			);
			
			$this->assertEquals(
				true,
				Helper::StringNullOrEmpty("")
			);
			
			$this->assertEquals(
				false,
				Helper::StringNullOrEmpty(1)
			);
			
			$this->assertEquals(
				false,
				Helper::StringNullOrEmpty("Mario")
			);
		}

		public function testArrayNullOrEmptySuccess() {
			$this->assertEquals(
				true,
				Helper::ArrayNullOrEmpty(null)
			);
			
			$this->assertEquals(
				true,
				Helper::ArrayNullOrEmpty([])
			);
			
			$this->assertEquals(
				true,
				Helper::ArrayNullOrEmpty(json_decode(""))
			);
			
			$this->assertEquals(
				false,
				Helper::ArrayNullOrEmpty([
					"Mario"
				])
			);
			
			$this->assertEquals(
				false,
				Helper::ArrayNullOrEmpty(json_decode(json_encode([
					"Mario"
				])))
			);
		}

		public function testObjectNullOrEmptySuccess() {
			$this->assertEquals(
				true,
				Helper::ObjectNullOrEmpty(null)
			);
			
			$this->assertEquals(
				true,
				Helper::ObjectNullOrEmpty(json_decode(""))
			);
			
			$this->assertEquals(
				false,
				Helper::ObjectNullOrEmpty(json_decode("{}"))
			);
		}
		
	}
	