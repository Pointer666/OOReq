<?php

namespace OOReq\Response;

use OOReq\Header\Headerlist;
use OOReq\HTTPStatusCode;

final class StringValue extends AbstractResponse
{
	private $data = '';

	public function __construct(?string $input=null)
	{
		$this->data = $input;
	}

	public function createByRequest($body, Headerlist $Headers, HTTPStatusCode $Status, \DateInterval $RequestTime)
	{
		return new StringValue(trim($body));
	}

	public function __toString()
	{
		return $this->data;
	}
}