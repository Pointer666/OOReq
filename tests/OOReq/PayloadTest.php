<?php

namespace OOReq;


use OOReq\Data\DataAsGET;
use OOReq\Data\DataAsPOST;
use OOReq\Data\DataAsRawBodyPOST;
use OOReq\Header\Header;

class PayloadTest extends \PHPUnit\Framework\TestCase
{

	/**
	 *
	 */
	public function testAdd()
	{
		$Payload = new Payload(new DataAsGET('a', 'a'));

		$this->assertTrue($Payload->containsDataType(new DataAsGET()));
		$this->assertFalse($Payload->containsDataType(new DataAsPOST()));

		$NewPayload = $Payload->add(new DataAsPOST('b', 'b'));

		$this->assertTrue($NewPayload->containsDataType(new DataAsPOST()));
	}

	/**
	 *
	 */
	public function testAdd_EmptyObject()
	{
		$this->expectException(\UnexpectedValueException::class, 'You may not add an empty Data Object');
		$this->getExpectedExceptionCode(1);
		$Payload = new Payload();

		$Payload->add(new DataAsPOST());

	}

	/**
	 *
	 */
	public function testAdd_mixRawPostDataAndUrlencodedPostData()
	{
		$this->expectException(\UnexpectedValueException::class);
		$this->getExpectedExceptionCode(2);
		$Payload = new Payload();

		$Payload->add(new DataAsPOST('A', 'AValue'), new DataAsRawBodyPOST('Content'));

	}


	/**
	 *
	 */
	public function testIsEmpty()
	{
		$Payload = new Payload();
		$this->assertTrue($Payload->isEmpty());

		$NewPayload=$Payload->add(new DataAsGET('a', 'a'));
		$this->assertFalse($NewPayload->isEmpty());

	}

	public function testContainsDataType()
	{
		$GetData   = new DataAsGET('a', 'a');
		$PostData  = new DataAsPOST('b', 'b');
		$PostData2 = new DataAsPOST('c', 'c');
		$Header    = new Header('GreatCustomHeader', 'true');
		$Payload   = new Payload(
			$GetData,
			$PostData,
			$PostData2,
			$Header
		);

		$this->assertTrue($Payload->containsDataType(new DataAsGET()));
		$this->assertTrue($Payload->containsDataType(new DataAsPOST()));
		$this->assertTrue($Payload->containsDataType(new Header()));
		$this->assertFalse($Payload->containsDataType(new DataAsRawBodyPOST()));
	}

	/**
	 *
	 */
	public function testGetParametersByDataType()
	{
		$GetData   = new DataAsGET('a', 'a');
		$PostData  = new DataAsPOST('b', 'b');
		$PostData2 = new DataAsPOST('c', 'c');

		$Payload = new Payload(
			$GetData,
			$PostData,
			$PostData2
		);

		$getDataArray = $Payload->getParametersByDataType(new DataAsGET());
		$this->assertEquals([$GetData], $getDataArray);

		$postDataArray = $Payload->getParametersByDataType(new DataAsPOST());
		$this->assertEquals([$PostData, $PostData2], $postDataArray);
	}
}
