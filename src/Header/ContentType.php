<?php

namespace OOReq\Header;


class ContentType extends AbstractHeader
{
	protected $name = "Content-Type";


	public function createByString(string $headerLine): HeaderInterface
	{
		return new ContentType($this->_getValueFromString($headerLine));
	}
}