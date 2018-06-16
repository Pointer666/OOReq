<?php

namespace OOReq\CURL;


class CURL implements CURLInterface
{

	private $handle;
	private $Options;

	public function __construct(?CURLOptions $Options = null)
	{
		if (!is_null($Options))
		{
			// CURLOptions are not immutable. We want to get sure that
			// the options are not changed after creation
			$this->Options = clone $Options;
		}
		else
		{
			$this->Options = new CURLOptions();
		}
	}


	/**
	 * Creates a new instance of CURL.
	 * Helps to avoid the new operator.
	 * @param CURLOptions $Options
	 * @return CURLInterface
	 */
	public function new(CURLOptions $Options): CURLInterface
	{
		return new CURL($Options);
	}


	public function exec()
	{
		$this->handle = curl_init($this->Options->URL()->asString());
		curl_setopt_array($this->handle, $this->Options->asArray());
		return curl_exec($this->handle);
	}

	public function error()
	{
		return curl_error($this->handle);
	}

	public function errno(): int
	{
		return curl_errno($this->handle);
	}

	public function getinfo($options = null)
	{
		return curl_getinfo($this->handle, $options);
	}

	public function close()
	{
		curl_close($this->handle);
	}

	public function Options(): CURLOptions
	{
		return clone $this->Options;
	}
}