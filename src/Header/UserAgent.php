<?php

namespace OOReq\Header;


class UserAgent extends AbstractHeader
{
	protected $name = 'User-Agent';

	public function createByString(string $headerLine): HeaderInterface
	{
		return new UserAgent($this->_getValueFromString($headerLine));
	}

}