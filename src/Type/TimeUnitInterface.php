<?php

namespace OOReq\Type;


interface TimeUnitInterface
{
	public function createFromMicrotime(float $microtime):TimeUnitInterface;
	public function asInt(): int;

	public function asFloat(): float;
}