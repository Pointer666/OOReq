<?php

use OOReq\RequestInterface;
use OOReq\ResponseTransformation\AbstractTransformation;

class WeatherService
{
	/**
	 * @var RequestInterface
	 */
	private $Request;

	/**
	 * WeatherService constructor.
	 * @param RequestInterface $
	 */
	public function __construct(RequestInterface $Request)
	{
		$this->Request = $Request;
	}

	public function getTemperatureForZip(int $zip): ValueAsTemperature
	{
		$Payload = new \OOReq\Payload(new \OOReq\DataAsGET('zip', $zip));
		$Request = $this->Request->new(new URL('http://localhost:8000/WeatherService.php'), new GET(), $Payload);
		return $Request->getResponseUsing(new Temperature());
	}
}

class Temperature extends AbstractTransformation
{

	public function transform($body, \OOReq\Header\Headerlist $Headers, \OOReq\HTTPStatusCode $Status, \OOReq\Type\TimePeriod $RequestTime)
	{
		if(!$Status->isOK())
		{
			throw new \Exception('http status not OK');
		}

		$temp = json_decode($body, true);
		if (is_null($temp))
		{
			throw new \Exception('invalid json');
		}

		return new ValueAsTemperature((int)$temp['temperature']);
	}
}

class ValueAsTemperature
{
	/**
	 * @var int
	 */
	private $temp;

	public function __construct(int $temp)
	{

		$this->temp = $temp;
	}

	public function inCelsius(): int
	{
		return $this->temp;
	}
}
