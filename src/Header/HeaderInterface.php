<?php

namespace OOReq\Header;


use OOReq\DataInterface;

interface HeaderInterface extends DataInterface
{
	public function createByString(string $headerLine): HeaderInterface;

	public function isEmpty(): bool;

	public function asString():string;
}