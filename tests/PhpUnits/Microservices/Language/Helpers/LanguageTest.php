<?php
	namespace RawadyMario\Tests\Language\Helpers;

	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Microservices\Language\Helpers\LanguageTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Language\Helpers\Language;
	use RawadyMario\Language\Models\Lang;

	final class LanguageTest extends TestCase {

		public function setUp(): void {
			Language::ClearAllowed();

			parent::setUp();
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

		public function testSetDefaultSuccess(): void {
			$this->assertEquals(
				Lang::EN,
				Language::GetDefault()
			);

			Language::SetDefault(Lang::AR);

			$this->assertEquals(
				Lang::AR,
				Language::GetDefault()
			);

			Language::SetDefault(Lang::EN);
		}

		public function testSetActiveSuccess(): void {
			$this->assertEquals(
				Lang::EN,
				Language::GetActive()
			);

			Language::SetActive(Lang::AR);

			$this->assertEquals(
				Lang::AR,
				Language::GetActive()
			);

			Language::SetActive(Lang::EN);
		}

		public function testAddToAllowedSuccess() {
			$this->assertEmpty(Language::GetAllowed());

			Language::AddToAllowed(Lang::EN);
			$this->assertCount(1, Language::GetAllowed());

			Language::AddToAllowed(Lang::AR);
			$this->assertCount(2, Language::GetAllowed());

			Language::AddToAllowed(Lang::EN);
			$this->assertCount(2, Language::GetAllowed());
		}

		public function testRemoveFromAllowedSuccess() {
			$this->assertEmpty(Language::GetAllowed());

			Language::AddToAllowed(Lang::EN);
			Language::AddToAllowed(Lang::AR);
			Language::AddToAllowed(Lang::FR);
			$this->assertCount(3, Language::GetAllowed());

			Language::RemoveFromAllowed(Lang::FR);
			$this->assertCount(2, Language::GetAllowed());

			Language::RemoveFromAllowed(Lang::FR);
			$this->assertCount(2, Language::GetAllowed());
		}

	}
