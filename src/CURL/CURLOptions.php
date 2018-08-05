<?php

namespace OOReq\CURL;


use OOReq\URL;

class CURLOptions implements \Iterator, \Countable
{
	private $URL;
	private $Data;

	public function __construct(?URL $URL = null)
	{
		$this->URL  = $URL;
		$this->Data = new \ArrayIterator();
	}

	public function URL(): URL
	{
		return $this->URL;
	}

	public function setOpt($option, $value)
	{
		$this->Data[$option] = $value;
	}

	public function asArray(): array
	{
		return $this->Data->getArrayCopy();
	}

	/**
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current()
	{
		return $this->Data->current();
	}

	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next()
	{
		$this->Data->next();
	}

	/**
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key()
	{
		return $this->Data->key();
	}

	/**
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid()
	{
		return $this->Data->valid();
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind()
	{
		$this->Data->rewind();
	}

	/**
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 * @since 5.1.0
	 */
	public function count()
	{
		return $this->Data->count();
	}

	public function asString(): string
	{
		$out = '';
		foreach ($this->asArray() as $key => $value)
		{
			$out .= $key . "=>";
			if (is_callable($value))
			{
				$out .= " Closure";
			}
			else
			{
				$out .= print_r($value, true);
			}
			$out.="\n";
		}
		return $out;
	}

}