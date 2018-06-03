<?php

namespace OOReq\Type;


class MilliSecond implements TimeUnitInterface
{
	private $microTime;

	/**
	 * Second constructor.
	 * @param $microTime
	 */
	public function __construct(?float $microTime=0)
	{
		$this->microTime = $microTime;
	}

	public function asInt(): int
	{
		return (int) round($this->microTime * 1000);
	}

	public function asFloat(): float
	{
		return (float) round($this->microTime *1000);
	}

	public function createFromMicrotime(float $microtime): TimeUnitInterface
	{
		return new MilliSecond($microtime);
	}
}