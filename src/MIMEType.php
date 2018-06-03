<?php

namespace OOReq;


class MIMEType
{
	/**
	 * @var string
	 */
	private $type;

	public function __construct(?string $type=null)
	{
		$this->type = $type;
	}

	public function asString(): string
	{
		if($this->type==null)
		{
			return "application/octet-stream";
		}
		return $this->type;
	}
}