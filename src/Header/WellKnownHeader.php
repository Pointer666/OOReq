<?php

namespace OOReq\Header;


interface WellKnownHeader
{
	public function __construct(?string $value = null, ?bool $wasjoined = false);

}