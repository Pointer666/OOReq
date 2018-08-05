<?php

namespace OOReq\Header;


class Header implements HeaderInterface
{
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var null|string
	 */
	private $value;
	/**
	 * @var bool|null
	 */
	private $wasjoined;


	public function __construct(?string $name = "", ?string $value = "", ?bool $wasjoined = false)
	{
		$this->name      = $name;
		$this->value     = $value;
		$this->wasjoined = $wasjoined;
	}

	public function name(): string
	{
		return $this->name;
	}

	public function value(): string
	{
		return $this->value;
	}

	public function createByString(string $headerLine): HeaderInterface
	{
		$pos = strpos($headerLine, ":");
		if ($pos === false)
		{
			return new Header(trim($headerLine), '');
		}
		$name  = substr($headerLine, 0, $pos);
		$value = substr($headerLine, $pos + 1);
		return new Header($name, trim($value));
	}

	public function isEmpty(): bool
	{
		return empty($this->name);
	}

	public function asString(): string
	{
		if($this->value=='')
		{
			return $this->name;
		}
		return $this->name . ": " . $this->value;
	}

	public function __toString()
	{
		return $this->asString();
	}

	public function asArray(): array
	{
		return [$this->name => $this->value];
	}

	public function wasJoined(): bool
	{
		return $this->wasjoined;
	}
}