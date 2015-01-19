<?php 	
ini_set ("display_errors", "1");
error_reporting(E_ALL);
$playlistItem = $_GET["playlist"];
//echo $playlistItem."<br /><br />";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Jonah - analysis engine</title>
	<link href="/jonahv1/css/main.css" type="text/css" rel="stylesheet">
   </head> 
<body>
<div id='' style='padding:5px;'><img src='/jonahv1/images/inspector.png' align='right' padding='6 6 6 6' /><br />
<?php 
// $playlistItem = /mnt/applications/vod/media/tvp/mtv/38/38-master.m3u8
//$correctedPlaylist = str_replace("mnt/applications/","",$playlistItem);

echo ("Showing playlist for: <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;<b><span style='color:#ffcc00'>|| </span>&nbsp;".$playlistItem."</b><br /><br /><br />");
	
$handle = fopen($playlistItem, 'r');
$contents = stream_get_contents($handle);
fclose($handle);
echo ("<div align='center' style='align:center;'><form><textarea id='showText' class='showText' cols='75' rows='20' style='overflow:auto;'>".$contents."</textarea></form></div>");
//echo ("<div align='center' style='align:center;'><pre>".$contents."</pre></div>");
?>
</div>
<div class='disclaimer' style='text-align:right;'>Analysis by Jonah&trade; 1.0 &nbsp;&nbsp;</div></div>
</body></html>