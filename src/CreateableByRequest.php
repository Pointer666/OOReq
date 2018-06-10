<?php

namespace OOReq;


use OOReq\Header\Headerlist;
use OOReq\HTTPStatusCode;
use OOReq\Type\TimePeriod;
use OOReq\Response\ResponseOptionsInterface;

interface CreateableByRequest
{

	/**
	 * Method is called when useStream() == true
	 * Should return an Callback which is responsible to handle
	 * the read data. Must return the exact bytes written.
	 * @param $data
	 * @return callable
	 */
	public function streamCallback(): callable;

	public function RequestOptions(): ResponseOptionsInterface;

	public function createByRequest($body, Headerlist $Headers, HTTPStatusCode $Status, TimePeriod $RequestTime);
}