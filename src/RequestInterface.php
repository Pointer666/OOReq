<?php

namespace OOReq;


use OOReq\CURL\CURLInterface;
use OOReq\HTTPMethod\MethodInterface;

interface RequestInterface
{
	public function new(URL $Url, ?MethodInterface $HTTPMethod = null, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newGET(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newPOST(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newPUT(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newDELETE(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newCONNECT(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newHEAD(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newOPTIONS(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newPATCH(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newTRACE(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function getResponseAs(CreateableByRequest $Transformation);

	public function URL(): URL;

	public function HTTPMethod(): MethodInterface;

	public function Payload(): PayloadInterface;

	public function Options(): RequestOptionsInterface;
}