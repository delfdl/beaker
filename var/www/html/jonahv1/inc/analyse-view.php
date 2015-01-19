<?php 

	$watchfolder = '/var/www/html/jonahv1/txtout/';
	$filesfound = glob($watchfolder."*.txt"); 						// get all files under watchfolder with a wildcard extension. (change for specific filetypes)
	//echo ("filesfound: ".$filesfound."<br /><br />");

 	foreach($filesfound as $filefound)								// repeat for each file found in the watchfolder
	{
	// check for media detection 
	// get file info (size, data modified)
		echo ("<br />filefound: <b>".$filefound."</b><br />");

		// exec ($ffmpeg_inspect); 									// RUN FFMPEG COMMAND to get info and output info into media info txt
		// $ffprobe = exec($ffprobe_inspect);						// comparison with ffprobe
		 															//
		$ffmpeg_array = file($filefound);								// read media info txt into array
	// echo ("ffmpegarray: ".$ffmpeg_array."<br /><br />");
	 
		$howmany=count($ffmpeg_array);
	// echo ("count: ".$howmany."<br />");						// count no of items in ffmpeg file
	 
	 	$i=0;
		$audioStreams = 0;
	 	foreach ($ffmpeg_array as $ffmpeg_key)
	 	{
	 		$i++;
	 //echo ("<b>[".$i."]</b> ".$ffmpeg_key);
	 //$location = strpos($ffmpeg_key,"video");
	 //echo (">".$location."<");
	 
	 		if ((strpos($ffmpeg_key,"Video")) && (strpos($ffmpeg_key,"Stream"))) 
	 		{
			echo ("<b>video found - [".$i."]</b> - ".$ffmpeg_key); 
			echo (" <b>- </b><br />");
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
			echo (" <b>- </b><br />");
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
			echo ("<b> - </b><br />"); 
			// extract duration
			// extract bitrate
			}
		} // end foreach ffmpeg_key
		echo ($audioStreams." audio channels found<br />");
		// return videosummary
		// return audiosummary
	} // end foreach filefound
?>