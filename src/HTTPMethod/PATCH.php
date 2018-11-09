<?php

namespace OOReq\HTTPMethod;


class PATCH implements HTTPMethod
{

	public function asString(): string
	{
		return "PATCH";
	}
}