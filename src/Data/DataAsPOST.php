<?php

namespace OOReq\Data;


final class DataAsPOST extends AbstractData
{
	public function isPOST(): bool
	{
		return true;
	}
}