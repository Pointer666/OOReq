<?php

namespace OOReq\Data;


use OOReq\Header\ContentType;

final class DataAsRawBodyPOST implements DataInterface
{

	private $data;
	/**
	 * @var null|string
	 */
	private $contentType;

	public function __construct($data = null, ?string $contentType = "text/plain")
	{
		$this->data        = $data;
		$this->contentType = $contentType;
	}

	public function name(): string
	{
		return 'RawBody';
	}

	public function value(): string
	{
		return $this->data;
	}

	public function asArray(): array
	{
		return [$this->name() => $this->value()];
	}

	public function length(): int
	{
		return strlen($this->data);
	}

	public function contentType(): ContentType
	{
		return new ContentType($this->contentType);
	}

	public function isEmpty(): bool
	{
		return empty($this->data);
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
		return true;
	}

	public function isHeader(): bool
	{
		return false;
	}
}