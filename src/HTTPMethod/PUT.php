<?php

namespace OOReq\HTTPMethod;


class PUT implements HTTPMethod
{
	public function asString(): string
	{
		return "PUT";
	}
}