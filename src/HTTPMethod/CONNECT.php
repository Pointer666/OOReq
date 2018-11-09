<?php

namespace OOReq\HTTPMethod;


class CONNECT implements HTTPMethod
{

	public function asString(): string
	{
		return "CONNECT";
	}
}