<?php

use OOReq\Data\DataAsGET;
use OOReq\Data\DataAsPOST;

class DataTest extends \PHPUnit\Framework\TestCase
{

	public function testGetData()
	{
		$Data = new DataAsGET('KEY', 'VALUE');
		$this->assertEquals('VALUE', $Data->value());
		$this->assertEquals('KEY', $Data->name());
		$this->assertEquals(['KEY' => 'VALUE'], $Data->asArray());
		$this->assertFalse($Data->isEmpty());

		$this->assertTrue((new DataAsGET())->isEmpty());

		$dataArray = $Data->createFromArray(['CoolKey0' => 'CoolValue0','CoolKey1' => 'CoolValue1']);
		foreach($dataArray as $key=>$Item)
		{
			$this->assertInstanceOf(DataAsGet::class, $Item);
			$this->assertEquals('CoolValue'.$key, $Item->value());
			$this->assertEquals('CoolKey'.$key, $Item->name());
		}

	}

	/**
	 *
	 */
	public function testDataAsPost()
	{
		$Data = new DataAsPOST('KEY', 'VALUE');
		$this->assertEquals('VALUE', $Data->value());
		$this->assertEquals('KEY', $Data->name());
		$this->assertEquals(['KEY' => 'VALUE'], $Data->asArray());
		$this->assertFalse($Data->isEmpty());

		$this->assertTrue((new DataAsPOST())->isEmpty());

		$dataArray = $Data->createFromArray(['CoolKey0' => 'CoolValue0','CoolKey1' => 'CoolValue1']);
		foreach($dataArray as $key=>$Item)
		{
			$this->assertInstanceOf(DataAsPOST::class, $Item);
			$this->assertEquals('CoolValue'.$key, $Item->value());
			$this->assertEquals('CoolKey'.$key, $Item->name());
		}
	}
}
