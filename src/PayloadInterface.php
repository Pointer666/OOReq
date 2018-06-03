<?php

namespace OOReq;


interface PayloadInterface extends \Iterator
{
	public function getParametersByDataType(DataInterface $Data): array;

	public function containsDataType(DataInterface $Data): bool;

	public function add(DataInterface ...$Data): void;

	public function isEmpty(): bool;
}