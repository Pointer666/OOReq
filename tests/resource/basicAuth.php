<?php

if (!isset($_SERVER['PHP_AUTH_USER']))
{
	header('WWW-Authenticate: Basic realm="of the mad god"');
	header('HTTP/1.0 401 Unauthorized');
	echo 'unauthorized';
	exit;
}
else
{
	if ($_SERVER['PHP_AUTH_USER'] == 'user' && $_SERVER['PHP_AUTH_PW'] == 'password')
	{
		echo "OK";
	}
	else
	{
		echo "Unknown password/user";
	}
}

