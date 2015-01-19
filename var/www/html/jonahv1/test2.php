<?php
$handle = fopen('/mnt/applications/vod/media/tvp/mtv/38/38-master.f4m', 'r');
$contents = stream_get_contents($handle);
fclose($handle);
echo ("stream_get_contents: ".$contents."<br /><br />");

$file=file_get_contents('/mnt/applications/vod/media/tvp/mtv/38/38-master.f4m');
echo ("file_get_contents: ".$file."<br /><br />");

?>