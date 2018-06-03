<?php

namespace OOReq\HTTPMethod;


class OPTIONS implements MethodInterface
{

	public function asString(): string
	{
		return "OPTIONS";
	}
}