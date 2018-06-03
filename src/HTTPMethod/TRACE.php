<?php

namespace OOReq\HTTPMethod;


class TRACE implements MethodInterface
{

	public function asString(): string
	{
		return "TRACE";
	}
}