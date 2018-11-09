<?php

namespace OOReq\Header;


use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{

	/**
	 *
	 */
	public function testBasics()
	{

		$Header = new Header('John', 'Doe');

		$this->assertEquals('John', $Header->name());
		$this->assertEquals('Doe', $Header->value());
		$this->assertFalse($Header->isEmpty());
		$this->assertFalse($Header->isGET());
		$this->assertFalse($Header->isPOST());
		$this->assertFalse($Header->isRAWPOST());
		$this->assertTrue($Header->isHeader());

		$this->assertEquals(['John' => 'Doe'], $Header->asArray());
		$this->assertEquals("John: Doe", $Header->asString());
		$this->assertEquals("John: Doe", (string) $Header);
	}
}
