<?php

namespace OOReq;


class RequestOptions implements RequestOptionsInterface
{
	/**
	 * Connection Timeout in MS
	 * @var int
	 */
	private $connectionTimeout = 0;
	private $referer = '';
	private $timeout = 0;

	public function timeout(): int
	{
		return $this->timeout;
	}


	public function settimeout(int $mseconds)
	{
		$this->timeout = $mseconds;
	}

	public function connectionTimeout(): int
	{
		return $this->connectionTimeout;
	}

	public function setConnectionTimeout(int $mseconds)
	{
		$this->connectionTimeout = $mseconds;
	}


	public function referer(): string
	{
		return $this->referer;
	}
}