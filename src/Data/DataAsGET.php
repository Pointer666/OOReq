<?php

namespace OOReq\Data;


final class DataAsGET extends AbstractData
{
	public function isGET(): bool
	{
		return true;
	}
}