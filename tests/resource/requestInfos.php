<?php

$out = ['_POST' => $_POST,
		'_GET'  => $_GET
];
echo json_encode($out);

