<?php

namespace OOReq\Type;


class TimePeriod
{
	/**
	 * @var float
	 */
	private $start;
	/**
	 * @var float
	 */
	private $end;

	/**
	 * TimePeriod constructor.
	 * @param float $start
	 * @param float $end
	 */
	public function __construct(float $start, float $end)
	{
		$this->start = $start;
		$this->end   = $end;
	}

	public function in(TimeUnitInterface $Unit): TimeUnitInterface
	{
		return $Unit->createFromMicrotime($this->end - $this->start);
	}
}