<?php

namespace OOReq\Header;


class ContentLength extends AbstractHeader
{
	protected $name = "Content-Length";

	public function createByString(string $headerLine): HeaderInterface
	{
		return new ContentLength($this->_getValueFromString($headerLine));
	}

}