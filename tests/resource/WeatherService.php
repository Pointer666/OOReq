<?php

$zip = $_REQUEST['zip'];

switch ($zip)
{
	case "123":
		$temp = 22;
		break;
	case "222":
		$temp = 27;
		break;
	case "666":
		die("broken json");
	case "667":
		http_response_code(418); // This is a Teapot
		echo json_encode(['teapot' => true]);
		break;
	default:
		$temp = 19;
		break;
}

echo json_encode(['temperature' => $temp]);

