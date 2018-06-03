<?php
$rawPost = file_get_contents("php://input");
echo "[" . $rawPost . "][" . $_SERVER['CONTENT_TYPE']."]";
