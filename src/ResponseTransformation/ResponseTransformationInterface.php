<?php

namespace OOReq\ResponseTransformation;


use OOReq\Header\Headerlist;
use OOReq\HTTPStatusCode;
use OOReq\Type\TimePeriod;

interface ResponseTransformationInterface
{

	/**
	 * Method is called when useStream() == true
	 * Should return an Callback which is responsible to handle
	 * the read data. Must return the exact bytes written.
	 * @param $data
	 * @return callable
	 */
	public function getCallback(): callable;

	public function RequestOptions(): TransformationOptionsInterface;

	public function transform($body, Headerlist $Headers, HTTPStatusCode $Status, TimePeriod $RequestTime);
}