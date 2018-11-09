<?php

namespace OOReq\Data;


interface DataInterface
{
	public function name(): string;

	public function value(): string;

	public function asArray(): array;

	public function isEmpty(): bool;

	public function isGET(): bool;

	public function isPOST(): bool;

	public function isRAWPOST(): bool;

	public function isHeader(): bool;
}