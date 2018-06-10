<?php

namespace OOReq\Response;

final class StringValue extends AbstractResponse
{
	public function createByRequest($body, $Header, $Status, $TImePeriod)
	{
		return trim($body);
	}
}