<?php

namespace OOReq;

use OOReq\ResponseTransformation\ResponseTransformationInterface;


interface ResponseInterface
{
	public function transform(ResponseTransformationInterface $Transformation);
}