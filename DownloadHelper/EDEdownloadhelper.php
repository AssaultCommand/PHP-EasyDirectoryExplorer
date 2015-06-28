<?
$yourfile = $_GET["file"];

header ("Content-Type: application/download");
header ("Content-Disposition: attachment; filename=$yourfile");
header("Content-Length: " . filesize("$yourfile"));
$fp = fopen("$yourfile", "r");
fpassthru($fp);
?>