<?php

namespace OOReq;


use OOReq\Header\Header;
use OOReq\Header\Headerlist;
use OOReq\ResponseTransformation\AbstractTransformation;
use OOReq\ResponseTransformation\AbstractTransformationOptions;
use OOReq\ResponseTransformation\ResponseTransformationInterface;
use OOReq\ResponseTransformation\TransformationOptionsInterface;
use OOReq\Type\TimePeriod;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
	private $CURL;
	private $Transformation;
	private $CURLOptions;

	public function setUp()
	{
		$this->CURL = $this->getMockBuilder(CURL::class)->disableOriginalConstructor()->getMock();
		$this->CURL->method('getinfo')->willReturn(200);
		$this->CURL->method('new')->willReturnSelf();
		$this->CURLOptions    = $this->getMockBuilder(CURLOptions::class)->disableOriginalConstructor()->getMock();
		$this->Transformation = $this->getMockBuilder(ResponseTransformationInterface::class)->getMock();
	}

	private function _getTransformationOptions($includeHeaders = false, $useStream = false): TransformationOptionsInterface
	{
		$TO = new class($includeHeaders, $useStream) implements TransformationOptionsInterface
		{
			private $includeHeaders;
			private $useStream;

			public function __construct($includeHeaders, $useStream)
			{
				$this->includeHeaders = $includeHeaders;
				$this->useStream      = $useStream;
			}

			/**
			 * Should the headers be fetched?
			 * @return bool
			 */
			public function includeHeaders(): bool
			{
				return $this->includeHeaders;
			}

			/**
			 * Should a stream be used to minimize memory usage?
			 * @return bool
			 */
			public function useStream(): bool
			{
				return $this->useStream;
			}
		};

		return $TO;
	}

	/**
	 * @covers \OOReq\Response
	 */
	public function testTransform_basic()
	{
		// No stream, no headers included
		$this->Transformation->expects($this->exactly(2))
			->method('RequestOptions')
			->willReturn($this->_getTransformationOptions());


		$this->CURLOptions->expects($this->exactly(2))
			->method('setOpt')
			->withConsecutive(
				[$this->equalTo(CURLOPT_HEADER), false]
			);

		$Response = new Response($this->CURLOptions, $this->CURL);
		$Response->transform($this->Transformation);

	}

	/**
	 * @covers \OOReq\Response
	 */
	public function testConnectionException_CurlExecFalse()
	{
		$this->expectException(ConnectionException::class);
		$this->expectExceptionMessage('ATerribleError');
		$this->getExpectedExceptionCode(2);

		$this->CURL->method('exec')->willReturn(false);
		$this->CURL->method('errno')->willReturn(2);
		$this->CURL->method('error')->willReturn('ATerribleError');

		$Response = new Response($this->CURL);
		$Response->transform($this->Transformation);
	}

	/**
	 * @covers \OOReq\Response
	 */
	public function testStream()
	{
		$this->Transformation->method('RequestOptions')->willReturn($this->_getTransformationOptions(true, true));

		$this->CURL->expects($this->exactly(3))
			->method('setOpt')
			->withConsecutive(
				[$this->equalTo(CURLOPT_WRITEFUNCTION), $this->isType('callable')],
				[$this->equalTo(CURLOPT_HEADER), true]
			);

		$Response = new Response($this->CURL);
		$Response->transform($this->Transformation);
	}


	public function testHeaders()
	{
		$this->CURL->method('setOpt')
			->will($this->returnCallback(function ($key, $value) {
				if ($key == CURLOPT_HEADERFUNCTION)
				{
					$value('channel', 'GreatHeader: Headercontent'); # Set Header
				}
				return true;
			}));

		$this->Transformation->expects($this->once())
			->method('transform')
			->with($this->isType('string'),
				$this->callback(function (Headerlist $HeaderList) {
					return ($HeaderList->get(new Header('GreatHeader'))->value() == 'Headercontent');
				}),
				$this->anything(),
				$this->anything());

		$Response = new Response($this->CURL);
		$Response->transform($this->Transformation);

	}

}
