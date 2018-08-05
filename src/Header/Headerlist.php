<?php

namespace OOReq\Header;

class Headerlist implements \Iterator, \Countable
{
	private $Iterator;
	private $index = [];

	public function __construct(HeaderInterface ...$headers)
	{
		$data = [];
		$key=0;
		foreach ($headers as $Header)
		{
			if (!$Header->isEmpty())
			{
				$data[] = $Header;
				if (key_exists($Header->name(), $this->index))
				{
					$this->index[$Header->name()][] = $key;
				}
				else
				{
					$this->index[$Header->name()] = [$key];
				}
				$key++;
			}
		}
		$this->Iterator = new \ArrayIterator($data);
	}

	public function get(HeaderInterface $Header): HeaderInterface
	{
		if ($this->containsHeader($Header))
		{
			$keys = $this->index[$Header->name()];
			if (count($keys) > 1)
			{
				$data = [];
				/**
				 * Joining multiple headers with the same name
				 */
				foreach ($keys as $key)
				{
					$data[] = ($this->Iterator->offsetGet($key))->value();
				}
				if ($Header instanceof WellKnownHeader)
				{
					$class = get_class($Header);
					return new $class($Header->value(), true);
				}
				else
				{
					return new Header($Header->name(), implode(",", $data), true);
				}
			}
			return $this->Iterator->offsetGet(array_pop($keys));
		}

		throw new NotFoundException('Header ' . $Header->name() . ' does not exist');
	}

	public function containsHeader(HeaderInterface $Header): bool
	{
		return (key_exists($Header->name(), $this->index));
	}

	public function asArray(): array
	{
		$out = [];

		/** @var Header $Header */
		foreach ($this->Iterator as $Header)
		{
			$out[] = $Header->asString();
		}
		return $out;
	}

	public function addHeader(HeaderInterface $Header): Headerlist
	{
		$data   = $this->Iterator->getArrayCopy();
		$data[] = $Header;
		return new Headerlist(...array_values($data));
	}

	public function replaceHeader(HeaderInterface $Header): Headerlist
	{
		if ($this->containsHeader($Header))
		{
			$data = $this->_removeHeader($Header);
		}
		$data[] = $Header;
		return new Headerlist(...array_values($data));
	}

	public function removeHeader(HeaderInterface $Header): Headerlist
	{
		if ($this->containsHeader($Header))
		{
			$data = $this->_removeHeader($Header);
			return new Headerlist(...array_values($data));
		}
		throw new NotFoundException('Header ' . $Header->name() . ' does not exist');
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
		return $this->Iterator->count();
	}

	/**
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current()
	{
		return $this->Iterator->current();
	}

	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next()
	{
		$this->Iterator->next();
	}

	/**
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key()
	{
		return $this->Iterator->key();
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
		return $this->Iterator->valid();
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind()
	{
		$this->Iterator->rewind();
	}

	private function _removeHeader(HeaderInterface $Header)
	{
		$keys = $this->index[$Header->name()];
		$data = $this->Iterator->getArrayCopy();
		foreach ($keys as $key)
		{
			unset($data[$key]);
		}
		return $data;
	}
}