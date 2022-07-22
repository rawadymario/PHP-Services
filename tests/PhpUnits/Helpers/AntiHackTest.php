<?php
	namespace RawadyMario\Tests\Helpers;

	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Helpers\AntiHackTest.php
	use PHPUnit\Framework\TestCase;
	use RawadyMario\Helpers\AntiHack;
use RawadyMario\Helpers\Helper;

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

		public function testCheckSimpleArrayWithCustomKeySuccess() {
			$_GET = [
				"key" => "SELECT * FROM `users` WHERE `email` = 'test@hotmail.com' AND `active` = 1 AND `verified` = 1",
				"limit" => '100',
				"offset" => '50',
				"page" => '1',
				"category" => "Software Engineer"
			];

			$_GET = AntiHack::Check($_GET, [
				'query'
			]);

			$expected = [
				"key" => Helper::TruncateStr("_select_ * _from_ `users` _where_ `email` = 'test@hotmail.com' and `active` = 1 and `verified` = 1", 50 + 6, ""), //+ 6: Number of underscores added
				"limit" => '100',
				"offset" => '50',
				"page" => '1',
				"category" => "Software Engineer"
			];

			$this->assertEqualsCanonicalizing($expected, $_GET);
		}

	}
