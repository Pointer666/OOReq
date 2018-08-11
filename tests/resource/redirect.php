<?php
$max = $_GET['max'] ?? 5;
$no  = ($_GET['count'] ?? 0) + 1;
if ($no <= $max)
{
	header("Location: http://localhost:8000/redirect.php?count=" . $no . "&max=" . $max);
}
else
{
	echo "Content of redirected Target";
}
