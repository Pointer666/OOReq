<?php

namespace OOReq\Header;


class UserAgent extends AbstractHTTPHeader
{
	protected $name = 'User-Agent';

	public function createByString(string $headerLine): HTTPHeader
	{
		return new UserAgent($this->_getValueFromString($headerLine));
	}

}