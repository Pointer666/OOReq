<?php

namespace OOReq\ResponseTransformation;


use OOReq\Header\Headerlist;
use OOReq\HTTPStatusCode;
use OOReq\Type\TimePeriod;

abstract class AbstractTransformation implements ResponseTransformationInterface
{


	/**
	 * Method is called when useStream() == true
	 * Should return an Callback which is responsible to handle
	 * the read data. Must return the exact bytes written.
	 * @param $data
	 * @return callable
	 */
	public function getCallback(): callable
	{
		return function ($ch, $data): int {
			return strlen($data);
		};
	}

	public function RequestOptions(): TransformationOptionsInterface
	{
		return new class extends AbstractTransformationOptions { };
	}

}