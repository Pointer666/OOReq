<?php

namespace OOReq\HTTPMethod;


class TRACE implements HTTPMethod
{

	public function asString(): string
	{
		return "TRACE";
	}
}