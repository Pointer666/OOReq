<?php

namespace OOReq;


abstract class AbstractData implements DataInterface
{
	protected $key;
	protected $value;

	public function __construct($key = null, $value = null)
	{
		$this->key   = $key;
		$this->value = $value;
	}

	public function createFromArray(array $array): array
	{
		$out = [];
		foreach ($array as $key => $value)
		{
			$name  = get_class($this);
			$out[] = new $name($key, $value);
		}
		return $out;
	}

	public function name(): string
	{
		return urlencode($this->key);
	}

	public function value(): string
	{
		return urlencode($this->value);
	}

	public function asArray(): array
	{
		return [$this->key => $this->value];
	}

	public function isEmpty(): bool
	{
		return (empty($this->name()) && empty($this->value()));
	}
}

