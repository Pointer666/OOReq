<?php

namespace OOReq;


interface DataInterface
{
	public function name(): string;

	public function value(): string;

	public function asArray():array;

	public function isEmpty():bool;
}