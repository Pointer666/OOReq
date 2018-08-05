<?php

namespace OOReq;

interface RequestOptionsInterface
{
	public function timeout(): int;

	public function settimeout(int $mseconds);

	public function connectionTimeout(): int;

	public function setConnectionTimeout(int $mseconds);

	public function referer(): string;

	public function setReferer(URL $Referer);

	public function Logger(): \Psr\Log\LoggerInterface;

	public function setLogger(\Psr\Log\LoggerInterface $Logger);
}