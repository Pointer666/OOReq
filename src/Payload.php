<?php

namespace OOReq;


use OOReq\Data\DataAsPOST;
use OOReq\Data\DataAsRawBodyPOST;
use OOReq\Data\DataInterface;

class Payload implements PayloadInterface
{
	private $data = [];
	private $typeIndex = [];

	private const POST = 'POST';
	private const HEADER = 'HEADER';
	private const GET = 'GET';
	private const RAWPOST = 'RAWPOST';

	/**
	 * Payload constructor.
	 * @param null[]|DataInterface[] $Data
	 */
	public function __construct(?DataInterface ...$Data)
	{
		if (is_array($Data))
		{
			foreach ($Data as $key => $Param)
			{
				if ($Param->isEmpty())
				{
					$msg = 'Trying to add empty data object of type: ' . $this->_getType($Param);
					throw new \UnexpectedValueException($msg, 1);
				}

				if ($Param->isRAWPOST() && $this->containsDataType(new DataAsPOST())
					|| $Param->isPOST() && $this->containsDataType(new DataAsRawBodyPOST())
				)
				{
					throw new \UnexpectedValueException('You may not mix urlencoded POSTdata and rawPOSTdata', 2);
				}

				$type = $this->_getType($Param);
				if (key_exists($type, $this->typeIndex))
				{
					$this->typeIndex[$type][] = $key;
				}
				else
				{
					$this->typeIndex[$type] = [$key];
				}
			}
			$this->data = $Data;
		}
	}


	private function _getType(DataInterface $Param)
	{
		switch (true)
		{
			case $Param->isRAWPOST():
				return self::RAWPOST;
			case $Param->isPOST():
				return self::POST;
			case $Param->isGET():
				return self::GET;
			case $Param->isHeader():
				return self::HEADER;
		}

		return "unknown";
	}


	public function add(DataInterface ...$Data): PayloadInterface
	{
		$newData = array_merge($this->data, $Data);

		return new Payload(...$newData);
	}


	public function getParametersByDataType(DataInterface $Data): array
	{
		if ($this->containsDataType($Data) == false)
		{
			return [];
		}

		$out = [];
		foreach ($this->typeIndex[$this->_getType($Data)] as $key)
		{
			$out[] = $this->data[$key];
		}
		return $out;
	}

	public function containsDataType(DataInterface $Data): bool
	{
		return key_exists($this->_getType($Data), $this->typeIndex);
	}

	public function isEmpty(): bool
	{
		return (count($this->data) == 0);
	}
}