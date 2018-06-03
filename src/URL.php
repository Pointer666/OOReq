<?php

namespace OOReq;


class URL
{
	private $url;
	private $parts;

	public function __construct(?string $url=null)
	{
		$this->url   = $url;
		$this->parts = parse_url($url);
	}

	public function asString(): string
	{
		return $this->url;
	}

	/**
	 * Found at php.net
	 * Credits go to thomas at gielfeldt dot com
	 * @param $parsed_url
	 * @return string
	 */
	private function _unparse_url($parsed_url)
	{
		$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
		$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
		$pass     = ($user || $pass) ? "$pass@" : '';
		$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}

	public function addParameters(array $params): URL
	{
		$url_parts = parse_url($this->url);
		parse_str($url_parts['query'] ?? '', $paramsOld);
		$paramsOld = array_merge($paramsOld, $params);

		$url_parts['query'] = http_build_query($paramsOld);

		return new URL($this->_unparse_url($url_parts));
	}

	public function containsBasicAuthData(): bool
	{
		return key_exists('user', $this->parts) && $this->parts['user'] != '';
	}


	public function basicAuthString(): string
	{
		return ($this->parts['user'] ?? '') . ":" . ($this->parts['pass'] ?? '');
	}
}