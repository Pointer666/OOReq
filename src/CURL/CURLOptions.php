<?php

namespace OOReq\CURL;


use OOReq\Type\AbstractList;
use OOReq\URL;

class CURLOptions extends AbstractList
{
	private $URL;

	public function __construct(?URL $URL=null)
	{
		$this->URL=$URL;
	}

	public function URL():URL
	{
		return $this->URL;
	}

	public function setOpt($option, $value)
	{
		$this->data[$option] = $value;
	}

	public function asArray():array
	{
		return $this->data;
	}
}