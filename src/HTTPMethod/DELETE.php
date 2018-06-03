<?php

namespace OOReq\HTTPMethod;


class DELETE implements MethodInterface
{

	public function asString(): string
	{
		return "DELETE";
	}
}