<?php

namespace OOReq\HTTPMethod;


class HEAD implements HTTPMethod
{

	public function asString(): string
	{
		return "HEAD";
	}
}