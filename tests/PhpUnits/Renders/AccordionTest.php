<?php
	namespace RawadyMario\Tests\Renders;

	//To Run: .\vendor/bin/phpunit .\tests\PhpUnits\Renders\AccordionTest.php

	use PHPUnit\Framework\TestCase;
	use RawadyMario\Renders\Accordion;

	class AccordionTest extends TestCase {

		public function testEmptyTabSuccess() {
			$accordion = new Accordion("default_tabs", "tabs1");
			$this->assertEmpty($accordion->Render());
		}
	}
