<?php

namespace OOReq;


use OOReq\HTTPMethod\HTTPMethod;

interface RequestInterface
{
	public function new(URL $Url, ?HTTPMethod $HTTPMethod = null, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newGET(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newPOST(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newPUT(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newDELETE(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newCONNECT(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newHEAD(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newOPTIONS(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newPATCH(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function newTRACE(URL $Url, ?PayloadInterface $Payload = null, ?RequestOptionsInterface $RequestOptions = null): RequestInterface;

	public function exchangePayload(PayloadInterface $Payload): RequestInterface;

	public function getResponseAs(CreateableByRequest $Transformation);

	public function URL(): URL;

	public function HTTPMethod(): HTTPMethod;

	public function Payload(): PayloadInterface;

	public function Options(): RequestOptionsInterface;
}