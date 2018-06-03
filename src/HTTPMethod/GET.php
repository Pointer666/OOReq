<?php

namespace OOReq\HTTPMethod;


class GET implements MethodInterface
{

	public function asString(): string
	{
		return "GET";
	}
}