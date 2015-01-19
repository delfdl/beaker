<?php 
	 ini_set ("display_errors", "1");
	 error_reporting(E_ALL);
	$analyseItem = $_GET["analysis"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Jonah - analysis engine</title>
	<link href="/jonahv1/css/main.css" type="text/css" rel="stylesheet">
   </head> 
<body>
<div id='' style='padding:5px;'><img src='/jonahv1/images/inspector.png' align='right' padding='9 9 9 9' /><br />
<?php 

	echo ("Showing ffmpeg_inspect for: <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;<b><span style='color:#ffcc00'>|| </span>&nbsp;".$analyseItem."</b><br /><br />");

    $filefound = "/var/www/html/".$analyseItem;
 															//
		$ffmpeg_array = file($filefound);								// read media info txt into array
	// echo ("ffmpegarray: ".$ffmpeg_array."<br /><br />");
	 
		$howmany=count($ffmpeg_array);
	// echo ("count: ".$howmany."<br />");						// count no of items in ffmpeg file
	?>
 <div id='' style='padding:5px; border:1px solid red; font-size:14px;'>
 <?php  
	 	$i=0;
		$audioStreams = 0;
	 	foreach ($ffmpeg_array as $ffmpeg_key)
	 	{
	 		$i++;
	 // echo ("<b>[".$i."]</b> ".$ffmpeg_key);
	 // $location = strpos($ffmpeg_key,"video");
	 // echo (">".$location."<");
	 
	 		if ((strpos($ffmpeg_key,"Video")) && (strpos($ffmpeg_key,"Stream"))) 
	 		{
			echo ("<b>video found - [".$i."]</b> - ".$ffmpeg_key); 
			echo ("<br /><br />");
			// extract video codec / profile
			// extract video bitrate
			// extract framerate
			// extract resolution
			// extract sd or hd
			// extract aspect ratio
			}
	 
	 		if ((strpos($ffmpeg_key,"Audio")) && (strpos($ffmpeg_key,"Stream"))) 
	 		{
			echo ("<b>audio found - [".$i."]</b> ".$ffmpeg_key); 
			echo ("<br /><br />");
			$audioStreams++; 
			// extract number of channels
			// extract audio codec / profile
			// extract sampling rate
			// extract audio bitrate
			// 
			}
	 
	 		if (strpos($ffmpeg_key,"Duration")) 
	 		{
			echo ("<b>duration found - [".$i."]</b> ".$ffmpeg_key); 
			echo ("<br /><br />"); 
			// extract duration
			// extract bitrate
			}
		} // end foreach ffmpeg_key
		echo ("<br /><b>".$audioStreams." audio streams found</b><br />");
		// return videosummary
		// return audiosummary

?></div>
</div>
<br />Displayed for debug purposes <br /><br />
<div id='' style='padding:5px;'><br />
<?php 

 															//
	$ffmpeg_array = file($filefound);								// read media info txt into array
	//echo ("ffmpegarray: ".$ffmpeg_array."<br /><br />");
	 
	$howmany=count($ffmpeg_array);
	//echo ("howmany: ".$howmany."<br /><br />");						// count no of items in ffmpeg file
	 
	 $i=0;
	 
?>
   <div id='' style='padding:5px; border:1px solid silver; font-size:9px;'>  
     
<?php
	 foreach ($ffmpeg_array as $ffmpeg_key)
	 {
	 $i++;
	 echo ("<b>[".$i."]</b> ".$ffmpeg_key."<br />");
	 }
	 
?>
</div>


<div class='disclaimer' style='text-align:right;'>Analysis by Jonah&trade; 1.0 &nbsp;&nbsp;</div></div>
</body></html>