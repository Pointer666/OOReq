<?php

use OOReq\HTTPMethod\GET;
use OOReq\HTTPMethod\POST;
use OOReq\HTTPMethod\PUT;
use OOReq\DataAsPOST;
use OOReq\FileAsPOST;
use OOReq\URL;
use OOReq\CURL;

class RequestTest extends \PHPUnit\Framework\TestCase
{
	private $_Responsetransformation;

	public function setUp()
	{

		$this->_Responsetransformation = new class() implements \OOReq\CreateableByRequest
		{
			public function streamCallback(): callable
			{
				return function ($data, $c) {
					return strlen($data);
				};
			}

			public function RequestOptions(): \OOReq\Response\ResponseOptionsInterface
			{
				return new class() implements \OOReq\Response\ResponseOptionsInterface
				{
					/**
					 * Should the headers be fetched?
					 * @return bool
					 */
					public function includeHeaders(): bool
					{
						return true;
					}

					/**
					 * Should a stream be used to minimize memory usage?
					 * @return bool
					 */
					public function useStream(): bool
					{
						return false;
					}
				};
			}

			public function createByRequest($body, \OOReq\Header\Headerlist $Headers, \OOReq\HTTPStatusCode $Status, \OOReq\Type\TimePeriod $RequestTime)
			{
				return ["body" => $body, "Headers" => $Headers, "Status" => $Status, "RequestTime", $RequestTime];
			}
		};
	}

	private function _getURL($basicAuth = false): URL
	{
		if ($basicAuth)
		{
			return new URL('http://user:passwd@localhost');
		}
		return new URL('http://localhost');
	}

	private function _getCURL($statusCode = 200, $responseString = 'RESPONSE')
	{
		$CURLMock = $this->getMockBuilder(\OOReq\CURL\CURLInterface::class)->getMock();
		$CURLMock->method('new')->willReturnSelf();
		$CURLMock->method('getInfo')->willReturn($statusCode);
		$CURLMock->method('exec')->willReturn($responseString);
		return $CURLMock;
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testBasic()
	{
		$URL      = $this->_getURL();
		$Request  = new \OOReq\Request($URL, null, null, null, $this->_getCURL());
		$response = $Request->getResponseAs($this->_Responsetransformation);

		$this->assertEquals($URL, $Request->URL());
		// Default method is GET
		$this->assertEquals(new GET(), $Request->HTTPMethod());
		// Payload should be empty
		$this->assertEquals(new \OOReq\Payload(), $Request->Payload());
		//Default Response. see $this->_getCURL()
		$this->assertEquals('RESPONSE', $response['body']);

		$this->assertInstanceOf(\OOReq\HTTPStatusCode::class, $response['Status']);
		// Status should be 200 = HTTP:OK
		$this->assertTrue($response['Status']->isOK());
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testPost()
	{
		$Curl                = $this->_getCURL();
		$ExpectedCurlOptions = new CURL\CURLOptions($this->_getURL());
		$ExpectedCurlOptions->setOpt(CURLOPT_POST, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_POSTFIELDS, ['testA' => 'DataA', 'testB' => 'DataB']);
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, []);
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});

		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);

		$Data     = new \OOReq\Payload(
			new DataAsPOST('testA', 'DataA'),
			new DataAsPOST('testB', 'DataB')
		);
		$Request  = new \OOReq\Request($this->_getURL(), new POST(), $Data, null, $Curl);
		$response = $Request->getResponseAs($this->_Responsetransformation);
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testBasicAuth()
	{
		// Get URL with user and password
		$Url = $this->_getURL(true);

		$Curl                = $this->_getCURL();
		$ExpectedCurlOptions = new CURL\CURLOptions($Url);
		$ExpectedCurlOptions->setOpt(CURLOPT_USERPWD, 'user:passwd');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, []);
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});

		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);

		$Request  = new \OOReq\Request($Url, new GET(), null, null, $Curl);
		$response = $Request->getResponseAs($this->_Responsetransformation);
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testPut()
	{
		$Url = $this->_getURL();

		$ExpectedCurlOptions = new CURL\CURLOptions($Url);
		$ExpectedCurlOptions->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, []);
		$ExpectedCurlOptions->setOpt(CURLOPT_POST, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});

		$Curl = $this->_getCURL();
		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);

		$Request  = new \OOReq\Request($Url, new PUT(), null, null, $Curl);
		$Response = $Request->getResponseAs($this->_Responsetransformation);
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testPostFile()
	{
		$Url = $this->_getURL();

		$ExpectedCurlOptions = new CURL\CURLOptions($Url);
		$ExpectedCurlOptions->setOpt(CURLOPT_POSTFIELDS, ['testA' => new CURLFile('/tmp/test', 'mimetype', 'test')]);
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, []);
		$ExpectedCurlOptions->setOpt(CURLOPT_POST, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});

		$Curl = $this->_getCURL();
		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);

		$Data = new \OOReq\Payload(
			new FileAsPOST('testA', new \SplFileObject('/tmp/test', 'w+'), new \OOReq\MIMEType('mimetype'))
		);

		$Request  = new \OOReq\Request($this->_getURL(), new POST(), $Data, null, $Curl);
		$Response = $Request->getResponseAs($this->_Responsetransformation);
	}

	private function _getRequestProto($Payload=null, $Options=null): \OOReq\Request
	{
		if(is_null($Options))
		{
			$Options=new \OOReq\RequestOptions();
		}
		if(is_null($Payload))
		{
			$Payload=new \OOReq\Payload();
		}
		$Curl         = $this->_getCURL();
		$RequestProto = new \OOReq\Request(null, null, $Payload, $Options, $Curl);
		return $RequestProto;
	}

	/**
	 * test the new$HTTPMETHOD methods
	 */
	public function testNewSomething()
	{
		$methods = ['GET', 'PUT', 'POST', 'TRACE', 'HEAD', 'OPTIONS', 'PATCH'];
		foreach ($methods as $method)
		{
			$methName = 'new' . $method;
			$Options  = new \OOReq\RequestOptions();
			$Payload  = new \OOReq\Payload(new DataAsPOST('param', 'value'));
			$Url      = new URL('http://example.com');

			$RequestProto = $this->_getRequestProto();

			$GETRequest = $RequestProto->$methName($Url, $Payload, $Options);

			$this->assertEquals($Url, $GETRequest->URL(), 'Method: '.$methName);
			$this->assertEquals($Payload, $GETRequest->Payload(), 'Method: '.$methName);
			$this->assertEquals($Options, $GETRequest->Options(), 'Method: '.$methName);
		}
	}

	/**
	 * test the new$HTTPMETHOD methods
	 */
	public function testNewSomething_usingPrototypeValues()
	{
		$methods = ['GET', 'PUT', 'POST', 'TRACE', 'HEAD', 'OPTIONS', 'PATCH'];
		foreach ($methods as $method)
		{
			$methName = 'new' . $method;
			$Options  = new \OOReq\RequestOptions();
			$Payload  = new \OOReq\Payload(new DataAsPOST('param', 'value'));
			$Url      = new URL('http://example.com');

			$RequestProto = $this->_getRequestProto($Payload,$Options);

			$GETRequest = $RequestProto->$methName($Url);

			$this->assertEquals($Url, $GETRequest->URL(), 'Method: '.$methName);
			$this->assertEquals($Payload, $GETRequest->Payload(), 'Method: '.$methName);
			$this->assertEquals($Options, $GETRequest->Options(), 'Method: '.$methName);
		}
	}
}