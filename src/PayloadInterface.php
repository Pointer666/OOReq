<?php

namespace OOReq;


use OOReq\Data\DataInterface;

interface PayloadInterface
{
	public function getParametersByDataType(DataInterface $Data): array;

	public function containsDataType(DataInterface $Data): bool;

	public function add(DataInterface ...$Data): PayloadInterface;

	public function isEmpty(): bool;
}