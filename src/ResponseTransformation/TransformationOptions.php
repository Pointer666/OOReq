<?php

namespace OOReq\ResponseTransformation;


final class TransformationOptions implements TransformationOptionsInterface
{

	/**
	 * Should the headers be fetched?
	 * @return bool
	 */
	public function includeHeaders(): bool
	{
		// TODO: Implement includeHeaders() method.
	}

	/**
	 * Should a stream be used to minimize memory usage?
	 * @return bool
	 */
	public function useStream(): bool
	{
		// TODO: Implement useStream() method.
	}
}