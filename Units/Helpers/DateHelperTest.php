<?php
	//To Run: .\vendor/bin/phpunit .\Units\Helpers\DateHelperTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\DateHelper;

	final class DateHelperTest extends TestCase {
		
		public function testCleanDateSuccess(): void {
			$this->assertEquals(
				"null",
				DateHelper::CleanDate(null)
			);
			
			$this->assertEquals(
				"null",
				DateHelper::CleanDate("")
			);
			
			$this->assertEquals(
				"'1992-01-07'",
				DateHelper::CleanDate("1992-01-07")
			);
		}
		
	}
	