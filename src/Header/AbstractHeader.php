<?php

namespace OOReq\Header;


abstract class AbstractHeader implements HeaderInterface
{
	protected $name = "Content-Type";
	protected $value;

	public function __construct(?string $value = null)
	{
		if ($value != null)
		{
			$this->value = $value;
		}
	}


	public function name(): string
	{
		return $this->name;
	}

	public function value(): string
	{
		return $this->value;
	}

	public function isEmpty(): bool
	{
		return is_null($this->value);
	}

	public function asString(): string
	{
		return $this->name . ": " . $this->value;
	}

	public function asArray(): array
	{
		return [$this->name => $this->value];
	}

	protected function _getValueFromString($headerLine)
	{
		$pos   = strpos($headerLine, ":");
		$value = substr($headerLine, $pos + 1);
		return $value;
	}

}