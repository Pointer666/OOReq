<?php

use OOReq\DataAsGET;
use OOReq\Header\Header;
use OOReq\Header\Headerlist;
use OOReq\HTTPMethod\POST;
use OOReq\HTTPMethod\PUT;
use OOReq\HTTPMethod\GET;
use OOReq\HTTPStatusCode;
use OOReq\MIMEType;
use OOReq\Response\AbstractResponse;
use OOReq\Response\File;
use OOReq\Payload;
use OOReq\DataAsPOST;
use OOReq\Request;
use OOReq\Response\StringValue;
use OOReq\URL;
use PHPUnit\Framework\TestCase;

/**
 * Class testBasics
 *
 * This tests require that the testserver is running. See ./resource/startServer.php
 *
 */
class IntegrationTest extends TestCase
{
	private const TESTHOST = 'localhost:8000';
	private const TMPFILE = '/tmp/testfile.tmp';
	/**
	 * @var Request
	 */
	private $Request;
	public static $pid;

	/**
	 *
	 */
	public static function setUpBeforeClass()
	{
		echo "starting local server\n";
		$command   = "cd " . BASEPATH . "/resource; php -S " . self::TESTHOST;
		$pid       = shell_exec(sprintf('%s > /dev/null 2>&1 & echo $!', $command));
		self::$pid = $pid;
		echo "PID: " . $pid;
	}

	public static function tearDownAfterClass()
	{
		echo "stopping local server\n";
		$cmd = "kill " . self::$pid;
		echo $cmd;
		shell_exec($cmd);
	}

	public function setUp()
	{
		$this->Request = new Request();
	}

	/**
	 * Just get the content of a Page
	 */
	public function testBasicGET()
	{
		$Request = new Request(new URL('http://' . self::TESTHOST . '/hallo'), null, null, $this->_getDefaultOptions());
		// Return the body as string
		$Result = $Request->getResponseAs(new StringValue());

		$this->assertEquals('hallo', (string)$Result);
	}

	public function testBasicPOST()
	{
		$URL = new URL('http://' . self::TESTHOST . '/postTest.php');

		$Payload = new Payload(
			...(new DataAsPOST())->createFromArray([
			'data' => 'POSTTEST',
			'd2'   => 'something'
		])
		);

		$Payload->add(new DataAsGET('getA', 'ValueA'));

		$Request = $this->Request->newPOST($URL, $Payload,$this->_getDefaultOptions());

		$Result = $Request->getResponseAs(new StringValue());

		$this->assertEquals('OK', $Result);
	}

	public function testBasicRawPOST()
	{
		$content     = "YEAAAHHH ü$";
		$contentType = "octet/stream";

		$Request = $this->Request->newPOST(
			new URL('http://' . self::TESTHOST . '/postTestRAW.php'),
			new Payload(new \OOReq\DataAsRawBodyPOST($content, $contentType)),
			$this->_getDefaultOptions()
		);

		$Result = $Request->getResponseAs(new StringValue());

		$this->assertEquals('[' . $content . '][' . $contentType . ']', $Result);

	}

	/**
	 * Just get the content of a Page with basic auth
	 */
	public function testBasicGET_withBasicAuth()
	{
		$Request = new Request(new URL('http://user:password@' . self::TESTHOST . '/basicAuth.php'),null,null,$this->_getDefaultOptions());

		// Return the body as StringValue
		$result = $Request->getResponseAs(new StringValue());
		$this->assertEquals('OK', $result);

		$Request = new Request(new URL('http://user:WRONGpassword@' . self::TESTHOST . '/basicAuth.php'));

		// Return the body as StringValue
		$result = $Request->getResponseAs(new StringValue());
		$this->assertEquals('Unknown password/user', $result);

	}


	public function testBigFile_stream()
	{
		$GETRequest = new Request(new URL('http://' . self::TESTHOST . "/bigFile.php"));

		/** @var \SplFileObject $OutFile */
		$OutFile = $GETRequest->getResponseAs(new File(self::TMPFILE, 'w+'));
		$this->assertInstanceof(\SplFileObject::class, $OutFile);
	}

	public function testBigFile_regular()
	{
		$GETRequest = new Request(new URL('http://' . self::TESTHOST . "/bigFile.php"));

		/** @var \SplFileObject $Out */
		$GETRequest->getResponseAs(new StringValue());
		$this->assertTrue(true);
	}

	/**
	 * see ./resource/headers.php
	 */
	public function testTransformationParams()
	{

		$TestTransformation =
			new class extends AbstractResponse
			{
				public function createByRequest($body, Headerlist $Headers, HTTPStatusCode $Status, $timePeriod)
				{
					return ($Status->isOK()
						&& $Headers->get(new Header('TestHeaderA'))->value() == 'testString'
						&& $Headers->get(new Header('TestHeaderB'))->value() == 'another StringValue'
						&& $Headers->get(new Header('TestHeaderC'))->value() == 'A, B, C'
					);
				}
			};

		$Request = new Request(new URL('http://' . self::TESTHOST . '/headers.php'));
		$this->assertTrue($Request->getResponseAs($TestTransformation));

	}

	public function testHeader()
	{
		$headerName  = 'greatHeader';
		$headerValue = 'greatValue';


		$Request = new Request(
			new Url('http://' . self::TESTHOST . '/returnHeader.php'),
			new GET(),
			new Payload(new Header($headerName, $headerValue))
		);

		$Res = $Request->getResponseAs(new StringValue());

		$this->assertEquals($headerName . ':' . $headerValue, $Res);
	}

	public function testIsATeapot()
	{
		$Request = new Request(new Url('http://' . self::TESTHOST . '/teapot.php'));

		$this->assertTrue($Request->getResponseAs(
			new class extends AbstractResponse
			{
				public function createByRequest($body, Headerlist $Headers, HTTPStatusCode $Status, \DateInterval $RequestTime)
				{
					return $Status->isI_AM_A_TEAPOT();
				}
			}
		));
	}

	/**
	 * see ./resource/returnMethod.php
	 */
	public function testMethod()
	{
		$Request = $this->Request->newPUT(new URL('http://' . self::TESTHOST . "/returnMethod.php"));

		$body = $Request->getResponseAs(new StringValue());

		$this->assertEquals('PUT', $body);
	}

	/**
	 *
	 */
	public function testFileUpload()
	{
		$filenema = BASEPATH . "/resource/upfile.txt";
		$File     = new SplFileObject($filenema);
		$Payload  = new Payload(new \OOReq\FileAsPOST(
			'greatFile',
			$File,
			new MIMEType('text/plain')));

		$TestPrinter = new class extends AbstractResponse
		{
			public function createByRequest($body, Headerlist $Headers, HTTPStatusCode $Status, \DateInterval $RequestTime)
			{

				$bodyDecoded = json_decode($body, true);
				return (
					$bodyDecoded['_FILES']['greatFile']['name'] == 'upfile.txt'
					&& $bodyDecoded['_FILES']['greatFile']['type'] == 'text/plain'
				);
			}
		};

		$Request = new Request(new URL('http://' . self::TESTHOST . "/fileUpload.php"), new POST(), $Payload);
		$this->assertTrue($Request->getResponseAs($TestPrinter));

	}

	/**
	 * Get Parameters should be joined
	 */
	public function testAddGetParam()
	{
		$URL     = new URL('http://' . self::TESTHOST . "/requestInfos.php?getA=hallo");
		$Payload = new Payload(new DataAsGET('getB', 'halloB'));

		$Request = new Request($URL, new PUT(), $Payload);
		$this->assertTrue(
			$Request->getResponseAs(
				new class extends AbstractResponse
				{
					public function createByRequest($body, Headerlist $Headers, HTTPStatusCode $Status, \DateInterval $RequestTime)
					{
						$bodyArr = json_decode($body, true);
						return (
							$bodyArr['_GET']['getA'] == 'hallo'
							&& $bodyArr['_GET']['getB'] == 'halloB'
						);
					}
				}
			)
		);
	}


	public function testCurlOptions_timeout()
	{
		$this->expectException(\OOReq\ConnectionException::class);
		$this->expectExceptionMessageRegExp("/^Resolving timed out/");
		$URL = new URL("http://unknownhost/request?getA=hallo");

		$Options = $this->_getDefaultOptions();
		$Options->setConnectionTimeout(1);

		$Request = new Request($URL,
			new PUT(),
			null,
			$Options
		);

		$res = $Request->getResponseAs(new StringValue());
	}

	private function _getDefaultOptions()
	{
		$Options = new \OOReq\RequestOptions();
		$Options->setLogger(
			new class extends \PSR\Log\AbstractLogger
			{
				public function log($level, $message, array $context = array())
				{
					echo $message."\n";
				}
			}
		);
		return $Options;
	}


}