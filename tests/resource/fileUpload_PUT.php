<?php
$data = file_get_contents('php://input');

$boundary = substr($_SERVER['CONTENT_TYPE'], strpos($_SERVER['CONTENT_TYPE'], 'boundary=') + 9);

$parts   = explode($boundary, $data);
$outData = [];
foreach ($parts as $key => $part)
{
	if (trim($part) == "")
	{
		continue;
	}

	$lines     = explode("\r\n", trim($part));
	$firstLine = false;
	foreach ($lines as $line)
	{
		if ($line == "\r\n" && $firstLine == false)
		{
			$firstLine = true;
			continue;
		}
		if (isset($outData[$key]))
		{
			$outData[$key] .= $line;
		}
		else
		{
			$outData[$key] = $line;
		}
	}
}

$out = [
	"_FILES"   => $_FILES,
	"_POST"    => $_POST,
	"data"     => $data,
	"boundary" => print_r($outData, true),
];


echo json_encode($out);

