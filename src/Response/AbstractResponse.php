<?php

namespace OOReq\Response;

use OOReq\CreateableByRequest;

abstract class AbstractResponse implements CreateableByRequest
{
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
			return strlen($data);
		};
	}

	public function RequestOptions(): ResponseOptionsInterface
	{
		return new class extends AbstractResponseOptions { };
	}

}