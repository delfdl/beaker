<html><head><title></title></head><body>
<h2>ingest2.ot2.tv (you accessed via <? echo $_SERVER['SERVER_NAME']; ?>)</h2>
<br /><br />
<div style='border:1 solid silver; padding:5px;font-size:10pt;'>
<?
$cmd='ffmpeg -version';
exec($cmd, $output, $returnvalue);

if ($returnvalue == 127) {
    echo 'ffmpeg not installed <br />';
}
else {
    echo '<b>ffmpeg found: </b><br />'; 
	foreach($output as $outputnode)		// repeat for each file found in the watchfolder
	{
	// check for media detection 
	// get file info (size, data modified)
		echo ("node: ".$outputnode."<br />");
	}
}


?>
</div>
<div style='border:1 solid silver; padding:5px;font-size:10pt;'>
<?
$cmd='mp4box';
exec($cmd, $output, $returnvalue);

if ($returnvalue == 127) {
    echo 'mp4box not installed <br />';
}
else {
    echo '<b>mp4box found: </b><br />'; 
	foreach($output as $outputnode)		// repeat for each file found in the watchfolder
	{
	// check for media detection 
	// get file info (size, data modified)
		echo ("node: ".$outputnode."<br />");
	}
}


?>

</div>
<div style='border:1 solid silver; padding:5px;font-size:10pt;'>
<?
$cmd='f4vpp';
exec($cmd, $output, $returnvalue);

if ($returnvalue == 127) {
    echo 'f4vpp not installed <br />';
}
else {
    echo '<b>f4vpp found: </b><br />'; 
	foreach($output as $outputnode)		// repeat for each file found in the watchfolder
	{
	// check for media detection 
	// get file info (size, data modified)
		echo ("node: ".$outputnode."<br />");
	}
}


?>
</div>

<p><a href='/sysinfo/'>sysinfo</a></p>
</body></html>
