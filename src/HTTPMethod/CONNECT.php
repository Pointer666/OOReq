<?php

namespace OOReq\HTTPMethod;


class CONNECT implements MethodInterface
{

	public function asString(): string
	{
		return "CONNECT";
	}
}