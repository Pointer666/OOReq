<?php

namespace OOReq\HTTPMethod;


class POST implements MethodInterface
{
	public function asString(): string
	{
		return "POST";
	}
}