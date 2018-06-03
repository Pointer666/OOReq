<?php

use OOReq\DataAsGET;
use OOReq\HTTPMethod\GET;
use OOReq\HTTPMethod\POST;
use OOReq\HTTPMethod\PUT;
use OOReq\DataAsPOST;
use OOReq\FileAsPOST;
use OOReq\URL;
use OOReq\CURL;

class RequestTest extends \PHPUnit\Framework\TestCase
{

	private $_CURL;

	public function setUp()
	{
		$this->_CURL = $this->getMockBuilder(\OOReq\CURLOptions::class)->disableOriginalConstructor()->getMock();
	}

	private function _getURL($basicAuth = false): URL
	{
		if ($basicAuth)
		{
			return new URL('http://user:passwd@localhost');
		}
		return new URL('http://localhost');
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testBasic()
	{

		$URL      = $this->_getURL();
		$Request  = new \OOReq\Request($URL, null, null);
		$Response = $Request->call();

		$this->assertInstanceOf(\OOReq\Response::class, $Response);
		$this->assertEquals($URL, $Request->URL());
		$this->assertEquals(new GET(), $Request->HTTPMethod());
		$this->assertEquals(new  \OOReq\Payload(new DataAsGET()), $Request->Payload());
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testPost()
	{
		$this->_CURL->expects($this->exactly(4))
			->method('setOpt')
			->withConsecutive(
				[CURLOPT_POST, true],
				[CURLOPT_POSTFIELDS, ['testA' => 'DataA', 'testB' => 'DataB']],
				[$this->equalTo(CURLOPT_HEADER), true],
				[CURLOPT_RETURNTRANSFER, true]
			);

		$Data     = new \OOReq\Payload(
			new DataAsPOST('testA', 'DataA'),
			new DataAsPOST('testB', 'DataB')
		);
		$Request  = new \OOReq\Request($this->_getURL(), new POST(), $Data, $this->_CURL);
		$Response = $Request->call();
		$this->assertInstanceOf(\OOReq\Response::class, $Response);
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testBasicAuth()
	{
		$this->_CURL->expects($this->exactly(3))
			->method('setOpt')
			->withConsecutive(
				[CURLOPT_USERPWD, 'user:passwd'],
				[$this->equalTo(CURLOPT_HEADER), true],
				[CURLOPT_RETURNTRANSFER, true]

			);

		$Request  = new \OOReq\Request($this->_getURL(true), new GET(), null, $this->_CURL);
		$Response = $Request->call();
		$this->assertInstanceOf(\OOReq\Response::class, $Response);
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testPut()
	{
		$this->_CURL->expects($this->exactly(3))
			->method('setOpt')
			->withConsecutive(
				[CURLOPT_CUSTOMREQUEST, 'PUT'],
				[$this->equalTo(CURLOPT_HEADER), true],
				[CURLOPT_RETURNTRANSFER, true]

			);

		$Request  = new \OOReq\Request($this->_getURL(), new PUT(), null, $this->_CURL);
		$Response = $Request->call();
		$this->assertInstanceOf(\OOReq\Response::class, $Response);
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testPostFile()
	{
		$this->_CURL->expects($this->exactly(4))
			->method('setOpt')
			->withConsecutive(
				[CURLOPT_POST, true],
				[CURLOPT_POSTFIELDS, ['testA' => new CURLFile('/tmp/test', 'mimetype', 'test')]],
				[$this->equalTo(CURLOPT_HEADER), true],
				[CURLOPT_RETURNTRANSFER, true]
			);

		$Data = new \OOReq\Payload(
			new FileAsPOST('testA', new \SplFileObject('/tmp/test', 'w+'), new \OOReq\MIMEType('mimetype'))
		);

		$Request  = new \OOReq\Request($this->_getURL(), new POST(), $Data, $this->_CURL);
		$Response = $Request->call();
		$this->assertInstanceOf(\OOReq\Response::class, $Response);
	}

}