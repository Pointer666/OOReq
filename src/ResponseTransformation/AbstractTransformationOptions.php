<?php

namespace OOReq\ResponseTransformation;


abstract class AbstractTransformationOptions implements TransformationOptionsInterface
{

	/**
	 * Should the headers be fetched?
	 * @return bool
	 */
	public function includeHeaders(): bool
	{
		return false;
	}


	/**
	 * Should a stream be used to minimize memory usage?
	 * @return bool
	 */
	public function useStream(): bool
	{
		return false;
	}
}