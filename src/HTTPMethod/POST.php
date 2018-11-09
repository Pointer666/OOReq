<?php

namespace OOReq\HTTPMethod;


class POST implements HTTPMethod
{
	public function asString(): string
	{
		return "POST";
	}
}