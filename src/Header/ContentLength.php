<?php

namespace OOReq\Header;


class ContentLength extends AbstractHTTPHeader
{
	protected $name = "Content-Length";

	public function createByString(string $headerLine): HTTPHeader
	{
		return new ContentLength($this->_getValueFromString($headerLine));
	}

}