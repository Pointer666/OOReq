<?php

namespace OOReq;


class PayloadTest extends \PHPUnit\Framework\TestCase
{

	public function testAdd()
	{
		$Payload = new Payload(new DataAsGET('a', 'a'));

		$this->assertTrue($Payload->containsDataType(new DataAsGET()));
		$this->assertFalse($Payload->containsDataType(new DataAsPOST()));

		$Payload->add(new DataAsPOST('b','b'));

		$this->assertTrue($Payload->containsDataType(new DataAsPOST()));
	}

	public function testContainsDataType()
	{
		$Payload = new Payload(new DataAsGET('a', 'a'));

		$this->assertTrue($Payload->containsDataType(new DataAsGET()));
		$this->assertFalse($Payload->containsDataType(new DataAsPOST()));

	}

	public function testIsEmpty()
	{
		$Payload = new Payload();
		$this->assertTrue($Payload->isEmpty());

		$Payload->add(new DataAsGET('a', 'a'));
		$this->assertFalse($Payload->isEmpty());

	}

	public function testGetParametersByDataType()
	{
		$GetData  = new DataAsGET('a', 'a');
		$PostData = new DataAsPOST('b', 'b');

		$Payload = new Payload(
			$GetData,
			$PostData
		);

		$getDataArray = $Payload->getParametersByDataType(new DataAsGET());
		$this->assertEquals([$GetData], $getDataArray);

		$postDataArray = $Payload->getParametersByDataType(new DataAsPOST());
		$this->assertEquals([$PostData], $postDataArray);
	}

}
