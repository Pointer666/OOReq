<?php

namespace OOReq\Header;


use OOReq\Type\AbstractList;

class Headerlist extends AbstractList
{
	public function __construct(HeaderInterface ...$headers)
	{
		$data = [];
		foreach ($headers as $Header)
		{
			if (!$Header->isEmpty())
			{
				$data[strtolower($Header->name())] = $Header;
			}
		}
		parent::__construct($data);
	}

	public function get(HeaderInterface $Header): HeaderInterface
	{
		if ($this->containsHeader($Header))
		{
			return $this->data[strtolower($Header->name())];
		}
		throw new NotFoundException('Header ' . $Header->name() . ' does not exist');
	}

	public function containsHeader(HeaderInterface $Header): bool
	{
		return key_exists(strtolower($Header->name()), $this->data);
	}

	public function asArray(): array
	{
		$out = [];

		/** @var Header $Header */
		foreach ($this->data as $Header)
		{
			$out[] = $Header->asString();
		}
		return $out;
	}

	public function addHeader(HeaderInterface $Header): Headerlist
	{
		$data                              = $this->data;
		$data[strtolower($Header->name())] = $Header;
		return new Headerlist(...$data);
	}

	public function removeHeader(HeaderInterface $Header): Headerlist
	{
		$data = $this->data;
		unset($data[strtolower($Header->name())]);
		return new Headerlist(...$data);
	}
}