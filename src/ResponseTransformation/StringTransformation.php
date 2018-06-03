<?php

namespace OOReq\ResponseTransformation;


final class StringTransformation extends AbstractTransformation
{
	public function transform($body, $Header, $Status, $TImePeriod)
	{
		return trim($body);
	}
}