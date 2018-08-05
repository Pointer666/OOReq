<?php

$chunkCount = 0;
while ($chunkCount < 1000)
{
	echo base64_encode(random_bytes(100*1024));
	ob_flush();

	$chunkCount++;
}
