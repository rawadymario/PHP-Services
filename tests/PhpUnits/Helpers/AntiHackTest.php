<?php
	namespace RawadyMario\Tests\Helpers;

	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Helpers\AntiHackTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\AntiHack;

	final class AntiHackTest extends TestCase {

		public function testCheckSimpleArraySuccess() {
			$_GET = [
				"key" => "SELECT * FROM `users`",
				"limit" => '100',
				"offset" => '50',
				"page" => '1',
				"category" => "Software Engineer"
			];

			$_GET = AntiHack::Check($_GET);

			$expected = [
				"key" => "_select_ * _from_ `users`",
				"limit" => '100',
				"offset" => '50',
				"page" => '1',
				"category" => "Software Engineer"
			];

			$this->assertEqualsCanonicalizing($expected, $_GET);
		}

		public function testCheckMultiArraySuccess() {
			$_GET = [
				"key" => "SELECT * FROM `users`",
				"limit" => '100',
				"offset" => '50',
				"page" => '1',
				"category" => "Software Engineer",
				"sub" => [
					"key" => "SELECT * FROM `users`",
					"sub" => [
						"key" => "SELECT * FROM `users`",
						"category" => "Software Engineer",
					]
				]
			];

			$_GET = AntiHack::Check($_GET);

			$expected = [
				"key" => "_select_ * _from_ `users`",
				"limit" => '100',
				"offset" => '50',
				"page" => '1',
				"category" => "Software Engineer",
				"sub" => [
					"key" => "_select_ * _from_ `users`",
					"sub" => [
						"key" => "_select_ * _from_ `users`",
						"category" => "Software Engineer",
					]
				]
			];

			$this->assertEqualsCanonicalizing($expected, $_GET);
		}

	}
