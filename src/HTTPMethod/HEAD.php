<?php

namespace OOReq\HTTPMethod;


class HEAD implements MethodInterface
{

	public function asString(): string
	{
		return "HEAD";
	}
}