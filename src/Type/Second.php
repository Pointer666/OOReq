<?php

namespace OOReq\Type;


class Second implements TimeUnitInterface
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
		return (int)round($this->microtime);
	}

	public function asFloat(): float
	{
		return (float)$this->microtime;
	}

	public function createFromMicrotime(float $microtime): TimeUnitInterface
	{
		return new Second($microtime);
	}
}