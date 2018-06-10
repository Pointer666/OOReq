<?php

namespace OOReq;

use OOReq\Response\CreateableByRequest;


interface ResponseInterface
{
	public function transform(CreateableByRequest $Transformation);
}