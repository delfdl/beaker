<?php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
$rndSeed = time();
for ($x = 1; $x <= 7; $x++) 
{
     $stream = 'aa-port'.$x.'-monitor';
     echo ($stream.'<br />');
     if ($x==4) 
     {
      // dont do owt because port4 stream doesnt exist (stream names are mapped to SDI connectors)
     } else 
     {
  		$fullString = "/home/ubuntu/bin/ffmpeg -i 'rtmp://rtmp-qa1.projectapollo2.com:8935/livecams/".$stream."' -vframes 1 -s 480x270 -vf select='eq(pict_type\,I)' -ss 2 -y -f image2 '/var/www/html/thumbs/".$stream.".jpg'";	
			//echo ($fullString.'<br />');
  		$success = exec($fullString,$miResult); // generate thumb from keyframe
  		echo ("<img src = '/thumbs/".$stream.".jpg' style='padding:2px;' />");
     }
}

	
?>