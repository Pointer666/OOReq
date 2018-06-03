<?php

namespace OOReq;

class HTTPStatusCode
{
	private $codes = ['CONTINUE'                                                  => 100,
					  'SWITCHING_PROTOCOLS'                                       => 101,
					  'PROCESSING'                                                => 102,
					  'OK'                                                        => 200,
					  'CREATED'                                                   => 201,
					  'ACCEPTED'                                                  => 202,
					  'NON_AUTHORITATIVE_INFORMATION'                             => 203,
					  'NO_CONTENT'                                                => 204,
					  'RESET_CONTENT'                                             => 205,
					  'PARTIAL_CONTENT'                                           => 206,
					  'MULTI_STATUS'                                              => 207,
					  'ALREADY_REPORTED'                                          => 208,
					  'IM_USED'                                                   => 226,
					  'MULTIPLE_CHOICES'                                          => 300,
					  'MOVED_PERMANENTLY'                                         => 301,
					  'FOUND'                                                     => 302,
					  'SEE_OTHER'                                                 => 303,
					  'NOT_MODIFIED'                                              => 304,
					  'USE_PROXY'                                                 => 305,
					  'RESERVED'                                                  => 306,
					  'TEMPORARY_REDIRECT'                                        => 307,
					  'PERMANENTLY_REDIRECT'                                      => 308,
					  'BAD_REQUEST'                                               => 400,
					  'UNAUTHORIZED'                                              => 401,
					  'PAYMENT_REQUIRED'                                          => 402,
					  'FORBIDDEN'                                                 => 403,
					  'NOT_FOUND'                                                 => 404,
					  'METHOD_NOT_ALLOWED'                                        => 405,
					  'NOT_ACCEPTABLE'                                            => 406,
					  'PROXY_AUTHENTICATION_REQUIRED'                             => 407,
					  'REQUEST_TIMEOUT'                                           => 408,
					  'CONFLICT'                                                  => 409,
					  'GONE'                                                      => 410,
					  'LENGTH_REQUIRED'                                           => 411,
					  'PRECONDITION_FAILED'                                       => 412,
					  'REQUEST_ENTITY_TOO_LARGE'                                  => 413,
					  'REQUEST_URI_TOO_LONG'                                      => 414,
					  'UNSUPPORTED_MEDIA_TYPE'                                    => 415,
					  'REQUESTED_RANGE_NOT_SATISFIABLE'                           => 416,
					  'EXPECTATION_FAILED'                                        => 417,
					  'I_AM_A_TEAPOT'                                             => 418,
					  'MISDIRECTED_REQUEST'                                       => 421,
					  'UNPROCESSABLE_ENTITY'                                      => 422,
					  'LOCKED'                                                    => 423,
					  'FAILED_DEPENDENCY'                                         => 424,
					  'RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL' => 425,
					  'UPGRADE_REQUIRED'                                          => 426,
					  'PRECONDITION_REQUIRED'                                     => 428,
					  'TOO_MANY_REQUESTS'                                         => 429,
					  'REQUEST_HEADER_FIELDS_TOO_LARGE'                           => 431,
					  'UNAVAILABLE_FOR_LEGAL_REASONS'                             => 451,
					  'INTERNAL_SERVER_ERROR'                                     => 500,
					  'NOT_IMPLEMENTED'                                           => 501,
					  'BAD_GATEWAY'                                               => 502,
					  'SERVICE_UNAVAILABLE'                                       => 503,
					  'GATEWAY_TIMEOUT'                                           => 504,
					  'VERSION_NOT_SUPPORTED'                                     => 505,
					  'VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL'                      => 506,
					  'INSUFFICIENT_STORAGE'                                      => 507,
					  'LOOP_DETECTED'                                             => 508,
					  'NOT_EXTENDED'                                              => 510,
					  'NETWORK_AUTHENTICATION_REQUIRED'                           => 511];
	/**
	 * @var int
	 */
	private $code;

	public function __construct(int $code)
	{
		$this->code = $code;
	}

	public function asInt(): int
	{
		return $this->code;
	}

	public function isCode(int $code): bool
	{
		return ($this->code == $code);
	}

	public function isCONTINUE()
	{
		return $this->isCode($this->codes['CONTINUE']);
	}

	public function isSWITCHING_PROTOCOLS()
	{
		return $this->isCode($this->codes['SWITCHING_PROTOCOLS']);
	}

	public function isPROCESSING()
	{
		return $this->isCode($this->codes['PROCESSING']);
	}

	public function isOK()
	{
		return $this->isCode($this->codes['OK']);
	}

	public function isCREATED()
	{
		return $this->isCode($this->codes['CREATED']);
	}

	public function isACCEPTED()
	{
		return $this->isCode($this->codes['ACCEPTED']);
	}

	public function isNON_AUTHORITATIVE_INFORMATION()
	{
		return $this->isCode($this->codes['NON_AUTHORITATIVE_INFORMATION']);
	}

	public function isNO_CONTENT()
	{
		return $this->isCode($this->codes['NO_CONTENT']);
	}

	public function isRESET_CONTENT()
	{
		return $this->isCode($this->codes['RESET_CONTENT']);
	}

	public function isPARTIAL_CONTENT()
	{
		return $this->isCode($this->codes['PARTIAL_CONTENT']);
	}

	public function isMULTI_STATUS()
	{
		return $this->isCode($this->codes['MULTI_STATUS']);
	}

	public function isALREADY_REPORTED()
	{
		return $this->isCode($this->codes['ALREADY_REPORTED']);
	}

	public function isIM_USED()
	{
		return $this->isCode($this->codes['IM_USED']);
	}

	public function isMULTIPLE_CHOICES()
	{
		return $this->isCode($this->codes['MULTIPLE_CHOICES']);
	}

	public function isMOVED_PERMANENTLY()
	{
		return $this->isCode($this->codes['MOVED_PERMANENTLY']);
	}

	public function isFOUND()
	{
		return $this->isCode($this->codes['FOUND']);
	}

	public function isSEE_OTHER()
	{
		return $this->isCode($this->codes['SEE_OTHER']);
	}

	public function isNOT_MODIFIED()
	{
		return $this->isCode($this->codes['NOT_MODIFIED']);
	}

	public function isUSE_PROXY()
	{
		return $this->isCode($this->codes['USE_PROXY']);
	}

	public function isRESERVED()
	{
		return $this->isCode($this->codes['RESERVED']);
	}

	public function isTEMPORARY_REDIRECT()
	{
		return $this->isCode($this->codes['TEMPORARY_REDIRECT']);
	}

	public function isPERMANENTLY_REDIRECT()
	{
		return $this->isCode($this->codes['PERMANENTLY_REDIRECT']);
	}

	public function isBAD_REQUEST()
	{
		return $this->isCode($this->codes['BAD_REQUEST']);
	}

	public function isUNAUTHORIZED()
	{
		return $this->isCode($this->codes['UNAUTHORIZED']);
	}

	public function isPAYMENT_REQUIRED()
	{
		return $this->isCode($this->codes['PAYMENT_REQUIRED']);
	}

	public function isFORBIDDEN()
	{
		return $this->isCode($this->codes['FORBIDDEN']);
	}

	public function isNOT_FOUND()
	{
		return $this->isCode($this->codes['NOT_FOUND']);
	}

	public function isMETHOD_NOT_ALLOWED()
	{
		return $this->isCode($this->codes['METHOD_NOT_ALLOWED']);
	}

	public function isNOT_ACCEPTABLE()
	{
		return $this->isCode($this->codes['NOT_ACCEPTABLE']);
	}

	public function isPROXY_AUTHENTICATION_REQUIRED()
	{
		return $this->isCode($this->codes['PROXY_AUTHENTICATION_REQUIRED']);
	}

	public function isREQUEST_TIMEOUT()
	{
		return $this->isCode($this->codes['REQUEST_TIMEOUT']);
	}

	public function isCONFLICT()
	{
		return $this->isCode($this->codes['CONFLICT']);
	}

	public function isGONE()
	{
		return $this->isCode($this->codes['GONE']);
	}

	public function isLENGTH_REQUIRED()
	{
		return $this->isCode($this->codes['LENGTH_REQUIRED']);
	}

	public function isPRECONDITION_FAILED()
	{
		return $this->isCode($this->codes['PRECONDITION_FAILED']);
	}

	public function isREQUEST_ENTITY_TOO_LARGE()
	{
		return $this->isCode($this->codes['REQUEST_ENTITY_TOO_LARGE']);
	}

	public function isREQUEST_URI_TOO_LONG()
	{
		return $this->isCode($this->codes['REQUEST_URI_TOO_LONG']);
	}

	public function isUNSUPPORTED_MEDIA_TYPE()
	{
		return $this->isCode($this->codes['UNSUPPORTED_MEDIA_TYPE']);
	}

	public function isREQUESTED_RANGE_NOT_SATISFIABLE()
	{
		return $this->isCode($this->codes['REQUESTED_RANGE_NOT_SATISFIABLE']);
	}

	public function isEXPECTATION_FAILED()
	{
		return $this->isCode($this->codes['EXPECTATION_FAILED']);
	}

	public function isI_AM_A_TEAPOT()
	{
		return $this->isCode($this->codes['I_AM_A_TEAPOT']);
	}

	public function isMISDIRECTED_REQUEST()
	{
		return $this->isCode($this->codes['MISDIRECTED_REQUEST']);
	}

	public function isUNPROCESSABLE_ENTITY()
	{
		return $this->isCode($this->codes['UNPROCESSABLE_ENTITY']);
	}

	public function isLOCKED()
	{
		return $this->isCode($this->codes['LOCKED']);
	}

	public function isFAILED_DEPENDENCY()
	{
		return $this->isCode($this->codes['FAILED_DEPENDENCY']);
	}

	public function isRESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL()
	{
		return $this->isCode($this->codes['RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL']);
	}

	public function isUPGRADE_REQUIRED()
	{
		return $this->isCode($this->codes['UPGRADE_REQUIRED']);
	}

	public function isPRECONDITION_REQUIRED()
	{
		return $this->isCode($this->codes['PRECONDITION_REQUIRED']);
	}

	public function isTOO_MANY_REQUESTS()
	{
		return $this->isCode($this->codes['TOO_MANY_REQUESTS']);
	}

	public function isREQUEST_HEADER_FIELDS_TOO_LARGE()
	{
		return $this->isCode($this->codes['REQUEST_HEADER_FIELDS_TOO_LARGE']);
	}

	public function isUNAVAILABLE_FOR_LEGAL_REASONS()
	{
		return $this->isCode($this->codes['UNAVAILABLE_FOR_LEGAL_REASONS']);
	}

	public function isINTERNAL_SERVER_ERROR()
	{
		return $this->isCode($this->codes['INTERNAL_SERVER_ERROR']);
	}

	public function isNOT_IMPLEMENTED()
	{
		return $this->isCode($this->codes['NOT_IMPLEMENTED']);
	}

	public function isBAD_GATEWAY()
	{
		return $this->isCode($this->codes['BAD_GATEWAY']);
	}

	public function isSERVICE_UNAVAILABLE()
	{
		return $this->isCode($this->codes['SERVICE_UNAVAILABLE']);
	}

	public function isGATEWAY_TIMEOUT()
	{
		return $this->isCode($this->codes['GATEWAY_TIMEOUT']);
	}

	public function isVERSION_NOT_SUPPORTED()
	{
		return $this->isCode($this->codes['VERSION_NOT_SUPPORTED']);
	}

	public function isVARIANT_ALSO_NEGOTIATES_EXPERIMENTAL()
	{
		return $this->isCode($this->codes['VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL']);
	}

	public function isINSUFFICIENT_STORAGE()
	{
		return $this->isCode($this->codes['INSUFFICIENT_STORAGE']);
	}

	public function isLOOP_DETECTED()
	{
		return $this->isCode($this->codes['LOOP_DETECTED']);
	}

	public function isNOT_EXTENDED()
	{
		return $this->isCode($this->codes['NOT_EXTENDED']);
	}

	public function isNETWORK_AUTHENTICATION_REQUIRED()
	{
		return $this->isCode($this->codes['NETWORK_AUTHENTICATION_REQUIRED']);
	}

	public function isInformational(): bool
	{
		return ($this->code < 100);
	}

	public function isSuccess(): bool
	{
		return ($this->code < 300 && $this->code >= 200);
	}

	public function isRedirection(): bool
	{
		return ($this->code < 400 && $this->code >= 300);
	}

	public function isClientError():bool
	{
		return ($this->code < 500 && $this->code >= 400);
	}

	public function isServerError():bool
	{
		return ($this->code < 600 && $this->code >= 500);
	}
}