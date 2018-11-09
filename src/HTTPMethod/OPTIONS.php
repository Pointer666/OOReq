<?php

namespace OOReq\HTTPMethod;


class OPTIONS implements HTTPMethod
{

	public function asString(): string
	{
		return "OPTIONS";
	}
}