<?php

namespace OOReq;


use OOReq\Header\HeaderInterface;

class Payload implements PayloadInterface
{
	private $data = [];

	private const POST = 'POST';
	private const HEADER = 'HEADER';
	private const GET = 'GET';
	private const RAWPOST = 'RAWPOST';

	/**
	 * Payload constructor.
	 */
	public function __construct(?DataInterface ...$Data)
	{
		if (is_array($Data))
		{
			$this->add(...$Data);
		}
	}


	private function _getType($Param)
	{
		$classType = 'unknown';
		switch (true)
		{
			case  is_subclass_of($Param, HeaderInterface::class, false) || is_a($Param, HeaderInterface::class, false):
				$classType = self::HEADER;
				break;
			case is_subclass_of($Param, DataAsGET::class, false) || is_a($Param, DataAsGET::class, false):
				$classType = self::GET;
				break;
			case  is_subclass_of($Param, DataAsRawBodyPOST::class, false) || is_a($Param, DataAsRawBodyPOST::class, false):
				$classType = self::RAWPOST;
				break;
			case is_subclass_of($Param, DataAsPOST::class, false) || is_a($Param, DataAsPOST::class, false) ||
				is_a($Param, FileAsPOST::class):
				$classType = self::POST;
				break;
		}

		return $classType;
	}

	public function add(DataInterface ...$Data): void
	{
		foreach ($Data as $Param)
		{
			if ($Param->isEmpty())
			{
				throw new \UnexpectedValueException('Trying to add empty data object of type: ' . $this->_getType($Param), 1);
			}

			$classType = $this->_getType($Param);

			if ($classType == self::POST && key_exists(self::RAWPOST, $this->data)
				|| $classType == self::RAWPOST && key_exists(self::POST, $this->data)
			)
			{
				throw new \UnexpectedValueException('You may not mix urlencoded POSTdata and rawPOSTdata', 2);
			}
			if (key_exists($classType, $this->data))
			{
				$this->data[$classType][] = $Param;
			}
			else
			{
				$this->data[$classType] = [$Param];
			}
		}
	}


	public function getParametersByDataType(DataInterface $Data): array
	{
		$type = $this->_getType($Data);
		return $this->data[$type] ?? [];
	}

	public function containsDataType(DataInterface $Data): bool
	{
		$type = $this->_getType($Data);
		return key_exists($type, $this->data);
	}

	public function isEmpty(): bool
	{
		return (count($this->data) == 0);
	}
}