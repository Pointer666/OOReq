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


	public function __construct(?string $name = "", ?string $value = "")
	{
		$this->name  = $name;
		$this->value = $value;
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
		$pos   = strpos($headerLine, ":");
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
		return $this->name . ": " . $this->value;
	}

	public function asArray(): array
	{
		return [$this->name => $this->value];
	}
}