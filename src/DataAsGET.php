<?php

namespace OOReq;


final class DataAsGET extends AbstractData
{
	public function createFromArray(array $array): array
	{
		$out = [];
		foreach ($array as $key => $value)
		{
			$out[] = new DataAsGET($key, $value);
		}
		return $out;
	}

}