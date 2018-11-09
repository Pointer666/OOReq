<?php

namespace OOReq\Header;


abstract class AbstractHTTPHeader implements HTTPHeader,WellKnownHeader
{
	protected $name = "Content-Type";
	protected $value;
	/**
	 * @var bool|null
	 */
	private $wasjoined;

	public function __construct(?string $value = null, ?bool $wasjoined=false)
	{
		if ($value != null)
		{
			$this->value = $value;
		}
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

	public function isEmpty(): bool
	{
		return is_null($this->value);
	}

	public function asString(): string
	{
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

	protected function _getValueFromString($headerLine)
	{
		$pos   = strpos($headerLine, ":");
		$value = substr($headerLine, $pos + 1);
		return $value;
	}

	/**
	 * true if the header was joined together.
	 * That happens if you add the same header multiple times to an headerlist.
	 *
	 * @return bool
	 */
	public function wasJoined(): bool
	{
		return $this->wasjoined;
	}

	public function isGET(): bool
	{
		return false;
	}

	public function isPOST(): bool
	{
		return false;
	}

	public function isRAWPOST(): bool
	{
		return false;
	}

	public function isHeader(): bool
	{
		return true;
	}
}