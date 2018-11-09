<?php

namespace OOReq\HTTPMethod;


class GET implements HTTPMethod
{

	public function asString(): string
	{
		return "GET";
	}
}