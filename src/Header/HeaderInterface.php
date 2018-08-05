<?php

namespace OOReq\Header;


use OOReq\DataInterface;

interface HeaderInterface extends DataInterface
{
	public function createByString(string $headerLine): HeaderInterface;

	public function isEmpty(): bool;

	public function asString(): string;

	/**
	 * true if the header was joined together.
	 * That happens if you add the same header multiple times to an headerlist.
	 *
	 * @return bool
	 */
	public function wasJoined(): bool;
}