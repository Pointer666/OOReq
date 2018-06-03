<?php

namespace OOReq\HTTPMethod;


class PUT implements MethodInterface
{
	public function asString(): string
	{
		return "PUT";
	}
}