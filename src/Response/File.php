<?php

namespace OOReq\Response;
use OOReq\CreateableByRequest;
use OOReq\Header\Headerlist;
use OOReq\HTTPStatusCode;
use OOReq\Type\TimePeriod;

final class File extends \SplFileObject implements CreateableByRequest
{
	/**
	 * Implementation must return true if a stream should be used
	 * @return bool
	 */
	public function useStream(): bool
	{
		return true;
	}

	public function RequestOptions(): ResponseOptionsInterface
	{
		return new class extends AbstractResponseOptions
		{
			/**
			 * Should a stream be used to minimize memory usage?
			 * @return bool
			 */
			public function useStream(): bool
			{
				return true;
			}
		};
	}

	public function createByRequest($body, Headerlist $Headers, HTTPStatusCode $Status, TimePeriod $RequestTime)
	{
		$this->flock(LOCK_UN);
		return $this;
	}

	/**
	 * Method is called when useStream() == true
	 * Should return an Callback which is responsible to handle
	 * the read data. Must return the exact bytes written.
	 * @param $data
	 * @return callable
	 */
	public function streamCallback(): callable
	{
		return function ($ch, $data): int {
			return $this->fwrite($data);
		};
	}
}