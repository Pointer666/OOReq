<?php

namespace OOReq\CURL;

interface CURLInterface
{
	public function new(CURLOptions $Options): CURLInterface;

	public function exec();

	public function error();

	public function errno(): int;

	public function getinfo($options = null);

	public function Options(): CURLOptions;

	public function close();
}