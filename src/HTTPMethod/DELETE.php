<?php

namespace OOReq\HTTPMethod;


class DELETE implements HTTPMethod
{

	public function asString(): string
	{
		return "DELETE";
	}
}