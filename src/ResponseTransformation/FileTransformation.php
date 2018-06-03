<?php

namespace OOReq\ResponseTransformation;


final class FileTransformation implements ResponseTransformationInterface
{
	private $filename = '';
	private $fp;

	/**
	 * FilePrinter constructor.
	 * @param StringTransformation $string
	 */
	public function __construct(\SplFileObject $File)
	{
		$this->File = new \SplFileObject($File->getPathname(), 'w+');
		$this->File->flock(LOCK_EX);
	}

	/**
	 * Implementation must return true if a stream should be used
	 * @return bool
	 */
	public function useStream(): bool
	{
		return true;
	}

	public function RequestOptions(): TransformationOptionsInterface
	{
		return new class extends AbstractTransformationOptions
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

	public function transform($body, $Header, $Status, $TimePeriod)
	{
		$this->File->flock(LOCK_UN);
		return $this->File;
	}

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
			return $this->File->fwrite($data);
		};
	}
}