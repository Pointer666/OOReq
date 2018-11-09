<?php

namespace OOReq\Header;


class ContentType extends AbstractHTTPHeader
{
	protected $name = "Content-Type";


	public function createByString(string $headerLine): HTTPHeader
	{
		return new ContentType($this->_getValueFromString($headerLine));
	}
}