<?php

namespace OOReq;

use OOReq\CURL\CURL;
use OOReq\CURL\CURLInterface;
use OOReq\CURL\CURLOptions;
use OOReq\Header\ContentLength;
use OOReq\Header\Header;
use OOReq\Header\HeaderInterface;
use OOReq\Header\Headerlist;
use OOReq\HTTPMethod\CONNECT;
use OOReq\HTTPMethod\DELETE;
use OOReq\HTTPMethod\GET;
use OOReq\HTTPMethod\HEAD;
use OOReq\HTTPMethod\MethodInterface;
use OOReq\HTTPMethod\OPTIONS;
use OOReq\HTTPMethod\PATCH;
use OOReq\HTTPMethod\POST;
use OOReq\HTTPMethod\PUT;
use OOReq\HTTPMethod\TRACE;

class Request implements RequestInterface
{
	/**
	 * @var URL
	 */
	private $Url;
	/**
	 * @var MethodInterface
	 */
	private $HTTPMethod;
	/**
	 * @var null|PayloadInterface
	 */
	private $Payload;

	private $response = '';
	private $curlExecExecuted = false;
	private $httpCode;
	private $body;

	private $TimePeriod;
	private $CURLOptions;

	/**
	 * @var CURLInterface
	 */
	private $CURL;
	private $wasInitialized = false;

	public function __construct(?URL $Url = null,
								?MethodInterface $HTTPMethod = null,
								?PayloadInterface $Payload = null,
								?RequestOptionsInterface $RequestOptions = null,
								?CURLInterface $CURL = null)
	{
		if (is_null($Url))
		{
			$this->Url = new URL();
		}
		else
		{

			$this->Url = clone $Url;
		}
		if (is_null($HTTPMethod))
		{
			$this->HTTPMethod = new GET();
		}

		else
		{
			$this->HTTPMethod = clone $HTTPMethod;
		}
		if (is_null($Payload))
		{
			$this->Payload = new Payload();
		}
		else
		{
			$this->Payload = clone $Payload;
		}

		$this->CURLOptions = new CURLOptions($Url);

		if (is_null($CURL))
		{
			$this->CURL = new CURL();
		}
		else
		{
			$this->CURL = $CURL;
			$this->_useCurlOptionsFromCURL($CURL->Options());
		}

		if (is_null($RequestOptions))
		{
			$this->RequestOptions = new RequestOptions();
		}
		else
		{
			$this->RequestOptions = clone $RequestOptions;
		}
	}

	public function URL(): URL
	{
		return clone $this->Url;
	}

	public function HTTPMethod(): MethodInterface
	{
		return clone $this->HTTPMethod;
	}

	public function Payload(): PayloadInterface
	{
		return clone $this->Payload;
	}


	private function _setPostFields()
	{
		$PostRawData = new DataAsRawBodyPOST();
		if ($this->Payload->containsDataType($PostRawData))
		{
			/** @var DataAsRawBodyPOST $RAWPostData */
			$RAWPostData = $this->Payload->getParametersByDataType($PostRawData)[0]; // There may only be one RawPostData Object
			$this->CURLOptions->setOpt(CURLOPT_POSTFIELDS, $RAWPostData->value());

			$this->Payload->add($RAWPostData->contentType());
			$this->Payload->add(new ContentLength($RAWPostData->length()));
			return;
		}

		$PostUrlencodedData = new DataAsPOST();
		$postFields         = [];
		if ($this->Payload->containsDataType($PostUrlencodedData))
		{
			/** @var \OOReq\DataAsPOST $Item */
			foreach ($this->Payload->getParametersByDataType($PostUrlencodedData) as $Item)
			{
				$postFields = array_merge($postFields, $Item->asArray());
			}
		}

		$FileData = new FileAsPOST();
		if ($this->Payload->containsDataType($FileData))
		{
			foreach ($this->Payload->getParametersByDataType($FileData) as $Item)
			{
				$postFields = array_merge($postFields, $Item->asArray());
			}
		}


		if (is_array($postFields) && count($postFields) > 0)
		{
			$this->CURLOptions->setOpt(CURLOPT_POSTFIELDS, $postFields);
		}
	}

	private function _recreateCURLOptionsURLWithGETParameters(): CURLOptions
	{
		$params = [];
		//add Parameter from Payload
		/** @var DataInterface $Parameter */
		foreach ($this->Payload->getParametersByDataType(new DataAsGET()) as $Parameter)
		{
			$params = array_merge($params, $Parameter->asArray());
		}

		// If we've got no params to add
		if (count($params) == 0)
		{
			// use our old options
			return $this->CURLOptions;
		}

		$Url         = $this->Url->addParameters($params);
		$CurlOptions = new CURLOptions($Url);

		foreach ($this->CURLOptions->asArray() as $key => $value)
		{
			$CurlOptions->setOpt($key, $value);
		}
		return $CurlOptions;
	}

	/**
	 * @return CURLOptions
	 */
	private function _init()
	{
		$this->CURLOptions = $this->_recreateCURLOptionsURLWithGETParameters();

		if ($this->HTTPMethod instanceof PUT || $this->HTTPMethod instanceof POST || $this->Payload->containsDataType(new FileAsPOST()))
		{
			$this->CURLOptions->setOpt(CURLOPT_POST, true);
		}

		// Set unusual Methods
		if ($this->HTTPMethod instanceof POST == false && $this->HTTPMethod instanceof GET == false)
		{
			$this->CURLOptions->setOpt(CURLOPT_CUSTOMREQUEST, $this->HTTPMethod->asString());
		}

		$this->_setPostFields();

		if ($this->Url->containsBasicAuthData())
		{
			$this->CURLOptions->setOpt(CURLOPT_USERPWD, $this->Url->basicAuthString());
		}
		$this->_setHeader();
		$this->CURLOptions->setOpt(CURLOPT_RETURNTRANSFER, true);
		$this->wasInitialized = true;
	}


	public function getResponseAs(CreateableByRequest $Transformation)
	{
		if ($this->wasInitialized == false)
		{
			$this->_init();
		}

		if (!$this->curlExecExecuted || $Transformation->RequestOptions()->useStream())
		{
			if ($Transformation->RequestOptions()->useStream())
			{
				$this->CURLOptions->setOpt(CURLOPT_WRITEFUNCTION, $Transformation->streamCallback());
			}

			$this->CURLOptions->setOpt(CURLOPT_HEADER, $Transformation->RequestOptions()->includeHeaders());

			$headerLines = [];

			$this->CURLOptions->setOpt(CURLOPT_HEADERFUNCTION, function ($ch, $line) use (&$headerLines) {
				$EmptyHeader = new Header();
				if (trim($line) != '')
				{
					$headerLines[] = $EmptyHeader->createByString($line);
				}

				return strlen($line);
			});

			if ($this->RequestOptions->connectionTimeout() > 0)
			{
				$this->CURLOptions->setOpt(CURLOPT_CONNECTTIMEOUT_MS, $this->RequestOptions->connectionTimeout());
			}

			if ($this->RequestOptions->timeout() > 0)
			{
				$this->CURLOptions->setOpt(CURLOPT_TIMEOUT_MS, $this->RequestOptions->timeout());
				/*
				 * from http://php.net/manual/de/function.curl-setopt.php
				 * The problem is that on (Li|U)nix, when libcurl uses the standard name resolver, a SIGALRM is raised
				 * during name resolution which libcurl thinks is the timeout alarm. The solution is to disable signals
				 * using CURLOPT_NOSIGNAL.  Here's an example script that requests itself causing a 10-second delay so you can test timeouts:
				 */
				$this->CURLOptions->setOpt(CURLOPT_NOSIGNAL, 1);
			}

			if ($this->RequestOptions->referer() != '')
			{
				$this->CURLOptions->setOpt(CURLOPT_REFERER, $this->RequestOptions->referer());
			}

			$Start = \DateTime::createFromFormat('0.u00 U', microtime());

			$this->_performCurlRequest();
			$this->_log('debug', 'Got header: [' . $this->_formatHeaderLines($headerLines) . "]");
			$this->TimePeriod = $Start->diff(\DateTime::createFromFormat('0.u00 U', microtime()));
			$this->_log('debug', 'Request took ' . $this->TimePeriod->f . ' microseconds');
		}
		return $Transformation->createByRequest($this->_getResponseBody(), new HeaderList(...$headerLines), $this->_getStatus(), $this->TimePeriod);
	}

	private function _getFormattedCurlOptions(): string
	{
		return "curloptions: [URL: " . $this->CURLOptions->URL()->asString() ."\n". $this->CURLOptions->asString(). "]";
	}

	private function _log($level, $msg)
	{
		$this->RequestOptions->Logger()->$level('OOReq: ' . $msg);
	}

	private function _performCurlRequest()
	{

		$this->_log('debug', 'Using ' . $this->_getFormattedCurlOptions());

		$Curl = $this->CURL->new($this->CURLOptions);

		$this->response = $Curl->exec();

		if ($this->response === false)
		{
			$this->_log('error', 'curl error[' . $Curl->error() . "][" . $Curl->errno() . "] " . $this->_getFormattedCurlOptions());
			// an curl error occured
			throw new ConnectionException($Curl->error(), $Curl->errno());
		}

		$bodyStart = strpos($this->response, "\r\n\r\n");
		if ($bodyStart !== false)
		{
			// if Headers were present. skip \r\n\r\n
			$bodyStart += 4;
		}

		$this->body = substr($this->response, $bodyStart);
		$this->_log('debug', 'Got body: [' . trim($this->body) . ']');
		$this->httpCode = $Curl->getinfo(CURLINFO_HTTP_CODE);
		$this->_log('debug', 'Got HTTP Code: [' . $this->httpCode . ']');
	}

	private function _getResponseBody(): string
	{
		return $this->body;
	}


	private function _getStatus()
	{
		return new HTTPStatusCode($this->httpCode);
	}

	private function _setHeader()
	{
		$headers = [];
		/** @var HeaderInterface $Header */
		foreach ($this->Payload->getParametersByDataType(new Header()) as $Header)
		{
			$headers[] = $Header->asString();
		}
		$this->CURLOptions->setOpt(CURLOPT_HTTPHEADER, $headers);
	}

	private function _useCurlOptionsFromCURL(CURLOptions $Options)
	{
		foreach ($Options->asArray() as $option => $value)
		{
			$this->CURLOptions->setOpt($option, $value);
		}
	}

	public function newGET(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface
	{
		return $this->new($Url, new GET(), $Payload, $RequestOptions);
	}

	public function newPOST(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface
	{
		return $this->new($Url, new POST(), $Payload, $RequestOptions);
	}

	public function newPUT(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface
	{
		return $this->new($Url, new PUT(), $Payload, $RequestOptions);
	}

	public function newDELETE(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface
	{
		return $this->new($Url, new DELETE(), $Payload, $RequestOptions);
	}

	public function newCONNECT(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface
	{
		return $this->new($Url, new CONNECT(), $Payload, $RequestOptions);
	}

	public function newHEAD(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface
	{
		return $this->new($Url, new HEAD(), $Payload, $RequestOptions);
	}

	public function newOPTIONS(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface
	{
		return $this->new($Url, new OPTIONS(), $Payload, $RequestOptions);
	}

	public function newPATCH(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface
	{
		return $this->new($Url, new PATCH(), $Payload, $RequestOptions);
	}

	public function newTRACE(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface
	{
		return $this->new($Url, new TRACE(), $Payload, $RequestOptions);
	}

	/**
	 * Creates a new Request object from a prototype.
	 * @param URL $Url
	 * @param null|MethodInterface $HTTPMethod If null the value from the prototype is used
	 * @param null|PayloadInterface $Payload If null the value from the prototype is used
	 * @param null|RequestOptionsInterface $RequestOptions If null the value from the prototype is used
	 * @return RequestInterface
	 */
	public function new(URL $Url, ?MethodInterface $HTTPMethod = null, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface
	{
		if (is_null($HTTPMethod))
		{
			$HTTPMethod = $this->HTTPMethod;
		}

		if (is_null($Payload))
		{
			$Payload = $this->Payload;
		}

		if (is_null($RequestOptions))
		{
			$RequestOptions = $this->RequestOptions;
		}
		return new Request($Url, $HTTPMethod, $Payload, $RequestOptions, $this->CURL);
	}

	public function Options(): RequestOptionsInterface
	{
		return clone $this->RequestOptions;
	}

	private function _formatHeaderLines(array $headerLines)
	{
		$out = '';
		/** @var Header $Header */
		foreach ($headerLines as $Header)
		{
			$out .= $Header->asString() . "\n";
		}
		return $out;
	}
}

