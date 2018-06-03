<?php

namespace OOReq;


final class DataAsPOST extends AbstractData
{

	public function createFromArray(array $array):array
	{
		$out = [];
		foreach ($array as $key => $value)
		{
			$out[] = new DataAsPOST($key, $value);
		}
		return $out;
	}
}