<?php

namespace OOReq;


class URLTest extends \PHPUnit\Framework\TestCase
{
	/**
	 *
	 */
	public function testBasic()
	{
		$url = 'http://example.com:666/page?param=3';
		$URL = new URL($url);
		$this->assertEquals($url, $URL->asString());
		// URL is immutable
		$NewURL = $URL->addParameters(["newparam" => 4]);
		$this->assertEquals($url . "&newparam=4", $NewURL->asString());

		//Special characters must be urlencoded
		$NewURL = $URL->addParameters(['nöööp' => 'urlencodät']);
		$this->assertEquals($url . "&n%C3%B6%C3%B6%C3%B6p=urlencod%C3%A4t", $NewURL->asString());
	}

	/**
	 *
	 */
	public function testBasicAuth()
	{
		$URL = new URL('http://user:password@example.com');
		$this->assertTrue($URL->containsBasicAuthData());
		$this->assertEquals('user:password',$URL->basicAuthString());
	}
}
