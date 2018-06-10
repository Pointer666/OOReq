<?php

namespace OOReq\Response;


interface ResponseOptionsInterface
{
	/**
	 * Should the headers be fetched?
	 * @return bool
	 */
	public function includeHeaders(): bool;

	/**
	 * Should a stream be used to minimize memory usage?
	 * @return bool
	 */
	public function useStream(): bool;
}