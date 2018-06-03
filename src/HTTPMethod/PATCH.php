<?php

namespace OOReq\HTTPMethod;


class PATCH implements MethodInterface
{

	public function asString(): string
	{
		return "PATCH";
	}
}