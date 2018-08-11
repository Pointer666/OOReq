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
	private $_Responsetransformation;
	private $_ResponsetransformationUsingStream;

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

			public function createByRequest($body, \OOReq\Header\Headerlist $Headers, \OOReq\HTTPStatusCode $Status, \DateInterval $RequestTime)
			{
				return ["body" => $body, "Headers" => $Headers, "Status" => $Status, "RequestTime" => $RequestTime];
			}
		};

		$this->_ResponsetransformationUsingStream = new class() implements \OOReq\CreateableByRequest
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
						return true;
					}
				};
			}

			public function createByRequest($body, \OOReq\Header\Headerlist $Headers, \OOReq\HTTPStatusCode $Status, \DateInterval $RequestTime)
			{
				return ["body" => $body, "Headers" => $Headers, "Status" => $Status, "RequestTime" => $RequestTime];
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

	private function _getCURL($statusCode = 200, $responseString = 'RESPONSE', $errorcode = null, $errorString = null)
	{
		$CURLMock = $this->getMockBuilder(\OOReq\CURL\CURLInterface::class)->getMock();
		$CURLMock->method('new')->willReturnSelf();
		$CURLMock->method('getInfo')->willReturn($statusCode);

		if (is_null($errorcode) and is_null($errorString))
		{
			$CURLMock->method('exec')->willReturn($responseString);
		}
		else
		{
			$CURLMock->method('exec')->willReturn(false);
			$CURLMock->method('error')->willReturn($errorString);
			$CURLMock->method('errno')->willReturn($errorcode);
		}

		$CurlOptions = new CURL\CURLOptions();
		$CurlOptions->setOpt(CURLOPT_REFERER, 'http://curlopt_referer.test');

		$CURLMock->method('Options')->willReturn($CurlOptions);
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

		$this->assertInstanceOf(\DateInterval::class, $response['RequestTime']);
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testConnectionError()
	{
		// In case of a curl error, an ConnectionException will be thrown
		// with the curl errormessage und code
		$this->expectException(\OOReq\ConnectionException::class);
		$this->expectExceptionMessage('UglyError');
		$this->expectExceptionCode(666);

		$URL      = $this->_getURL();
		$Request  = new \OOReq\Request($URL, null, null, null, $this->_getCURL(null, null, 666, 'UglyError'));
		$response = $Request->getResponseAs($this->_Responsetransformation);
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testHeaders()
	{
		$Curl                = $this->_getCURL();
		$ExpectedCurlOptions = new CURL\CURLOptions($this->_getURL());
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, 'http://curlopt_referer.test');
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, [0 => 'myCustomHeader: myData', 1 => 'header2: headervalue2', 2 => 'header2: headervalue3']);

		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});

		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);
		$URL     = $this->_getURL();
		$Payload = new \OOReq\Payload(new \OOReq\Header\Header('myCustomHeader', 'myData'),
			new \OOReq\Header\Header('header2', 'headervalue2'),
			new \OOReq\Header\Header('header2', 'headervalue3')
		);
		$Request = new \OOReq\Request($URL, new GET(), $Payload, null, $Curl);

		$response = $Request->getResponseAs($this->_Responsetransformation);

	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testRAWPost()
	{
		$postData      = "something";
		$mimetype      = "text/plain";
		$contentLength = strlen($postData);

		$Curl                = $this->_getCURL();
		$ExpectedCurlOptions = new CURL\CURLOptions($this->_getURL());
		$ExpectedCurlOptions->setOpt(CURLOPT_POST, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_POSTFIELDS, $postData);
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, [0 => 'Content-Type: ' . $mimetype, 1 => 'Content-Length: ' . $contentLength]);
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, 'http://curlopt_referer.test');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});
		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);

		$Data     = new \OOReq\Payload(
			new \OOReq\DataAsRawBodyPOST($postData, $mimetype)
		);
		$Request  = new \OOReq\Request($this->_getURL(), new POST(), $Data, null, $Curl);
		$response = $Request->getResponseAs($this->_Responsetransformation);
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
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, 'http://curlopt_referer.test');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});
		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

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
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, 'http://curlopt_referer.test');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});
		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

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
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, 'http://curlopt_referer.test');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});
		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

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
	public function testConnectionTimeout()
	{
		$Url     = $this->_getURL();
		$timeout = 400;

		$ExpectedCurlOptions = new CURL\CURLOptions($Url);
		$ExpectedCurlOptions->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, []);
		$ExpectedCurlOptions->setOpt(CURLOPT_POST, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, 'http://curlopt_referer.test');
		$ExpectedCurlOptions->setOpt(CURLOPT_CONNECTTIMEOUT_MS, $timeout);

		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});
		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

		$Curl = $this->_getCURL();
		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);

		$RQOptions = new \OOReq\RequestOptions();
		$RQOptions->setConnectionTimeout($timeout);

		$Request  = new \OOReq\Request($Url, new PUT(), null, $RQOptions, $Curl);
		$Response = $Request->getResponseAs($this->_Responsetransformation);
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testCustomReferer()
	{
		$Url     = $this->_getURL();
		$referer = 'http://new_referer.test';

		$ExpectedCurlOptions = new CURL\CURLOptions($Url);
		$ExpectedCurlOptions->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, []);
		$ExpectedCurlOptions->setOpt(CURLOPT_POST, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, $referer);

		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});
		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

		$Curl = $this->_getCURL();
		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);

		$RQOptions = new \OOReq\RequestOptions();
		$RQOptions->setReferer(new URL($referer));

		$Request  = new \OOReq\Request($Url, new PUT(), null, $RQOptions, $Curl);
		$Response = $Request->getResponseAs($this->_Responsetransformation);
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testCustomReferer_immutableOptions()
	{
		$Url     = $this->_getURL();
		$referer = 'http://new_referer.test';

		$ExpectedCurlOptions = new CURL\CURLOptions($Url);
		$ExpectedCurlOptions->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, []);
		$ExpectedCurlOptions->setOpt(CURLOPT_POST, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, $referer);

		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});
		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

		$Curl = $this->_getCURL();
		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);

		$RQOptions = new \OOReq\RequestOptions();
		$RQOptions->setReferer(new URL($referer));

		$Request = new \OOReq\Request($Url, new PUT(), null, $RQOptions, $Curl);

		// Changing the option object should not change the Request;
		$RQOptions->setReferer(new URL('somethingDifferent'));

		$Response = $Request->getResponseAs($this->_Responsetransformation);
	}


	/**
	 * @covers \OOReq\Request
	 */
	public function testTimeout()
	{
		$Url     = $this->_getURL();
		$timeout = 400;

		$ExpectedCurlOptions = new CURL\CURLOptions($Url);
		$ExpectedCurlOptions->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, []);
		$ExpectedCurlOptions->setOpt(CURLOPT_POST, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, 'http://curlopt_referer.test');

		$ExpectedCurlOptions->setOpt(CURLOPT_TIMEOUT_MS, $timeout);
		$ExpectedCurlOptions->setOpt(CURLOPT_NOSIGNAL, 1);

		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});
		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

		$Curl = $this->_getCURL();
		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);

		$RQOptions = new \OOReq\RequestOptions();
		$RQOptions->settimeout($timeout);

		$Request  = new \OOReq\Request($Url, new PUT(), null, $RQOptions, $Curl);
		$Response = $Request->getResponseAs($this->_Responsetransformation);
	}


	/**
	 * @covers \OOReq\Request
	 */
	public function testPostWithAddedGET()
	{
		$mimetype = 'mimetype';
		$filename = '/tmp/test.tmp';

		$Url                 = $this->_getURL();
		$UrlWithGetParam     = new URL($Url->asString() . '?paramA=paramAValue');
		$ExpectedCurlOptions = new CURL\CURLOptions($UrlWithGetParam);
		$ExpectedCurlOptions->setOpt(CURLOPT_POSTFIELDS, ['testA' => new CURLFile($filename, $mimetype, 'test.tmp')]);
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, []);
		$ExpectedCurlOptions->setOpt(CURLOPT_POST, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, 'http://curlopt_referer.test');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});
		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

		$Curl = $this->_getCURL();
		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);

		$Data = new \OOReq\Payload(
			new FileAsPOST('testA', new \SplFileObject($filename, 'w+'),
				new \OOReq\MIMEType($mimetype)),
			new DataAsGET('paramA', 'paramAValue')
		);

		$Request  = new \OOReq\Request($this->_getURL(), new POST(), $Data, null, $Curl);
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
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, 'http://curlopt_referer.test');
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});
		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

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


	private function _getRequestProto($Payload = null, $Options = null): \OOReq\Request
	{
		if (is_null($Options))
		{
			$Options = new \OOReq\RequestOptions();
		}
		if (is_null($Payload))
		{
			$Payload = new \OOReq\Payload();
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

			$this->assertEquals($Url, $GETRequest->URL(), 'Method: ' . $methName);
			$this->assertEquals($Payload, $GETRequest->Payload(), 'Method: ' . $methName);
			$this->assertEquals($Options, $GETRequest->Options(), 'Method: ' . $methName);
		}
	}

	/**
	 * test the new$HTTPMETHOD methods
	 */
	public function testNewSomething_usingPrototypeValues()
	{
		$methods = ['GET', 'PUT', 'POST', 'TRACE', 'HEAD', 'OPTIONS', 'PATCH', '', 'DELETE', 'CONNECT'];
		foreach ($methods as $method)
		{
			$methName = 'new' . $method;
			$Options  = new \OOReq\RequestOptions();
			$Payload  = new \OOReq\Payload(new DataAsPOST('param', 'value'));
			$Url      = new URL('http://example.com');

			$RequestProto = $this->_getRequestProto($Payload, $Options);

			$GETRequest = $RequestProto->$methName($Url);

			$this->assertEquals($Url, $GETRequest->URL(), 'Method: ' . $methName);
			$this->assertEquals($Payload, $GETRequest->Payload(), 'Method: ' . $methName);
			$this->assertEquals($Options, $GETRequest->Options(), 'Method: ' . $methName);
		}
	}

	/**
	 * @covers \OOReq\Request
	 */
	public function testTransformationUsesStream()
	{
		$URL = $this->_getURL();

		$ExpectedCurlOptions = new CURL\CURLOptions($URL);
		$ExpectedCurlOptions->setOpt(CURLOPT_HEADER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_HTTPHEADER, []);
		$ExpectedCurlOptions->setOpt(CURLOPT_REFERER, 'http://curlopt_referer.test');
		$ExpectedCurlOptions->setOpt(CURLOPT_WRITEFUNCTION, function () {
		});

		$ExpectedCurlOptions->setOpt(CURLOPT_HEADERFUNCTION, function () {
		});
		$ExpectedCurlOptions->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$ExpectedCurlOptions->setOpt(CURLOPT_MAXREDIRS,10); //

		$body   = "RESULT";
		$result = "Connection: keep-alive\n"
			. "Content-Encoding: gzip\n"
			. "Content-language: en\n"
			. "Content-Type: text/html; charset=utf-8\n"
			. "Date: Sun, 17 Jun 2018 14:10:40 GMT\r\n\r\n" . $body;
		$Curl   = $this->_getCURL(200, $result);
		$Curl->expects($this->exactly(1))
			->method('new')
			->with($ExpectedCurlOptions);

		$Request  = new \OOReq\Request($URL, null, null, null, $Curl);
		$response = $Request->getResponseAs($this->_ResponsetransformationUsingStream);
		$this->assertEquals($body, $response['body']);
	}
}