<?php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
   
  $thumbInterval = 60; // only generate new thumbs if over 60 seconds have passed
  $localpath      = '/var/www/html/';
  $rtmpjson       = $localpath.'test/data/rtmpcycle.json';					// local copy of nginx/stat
  $run_time       = date("U");
  $ffmpegPath     = '/home/ubuntu/bin/';
  $fireandforget  = '> /dev/null 2>/dev/null &' ; // add to exec (ffmpeg) call to make asynchronous (mmmm!) - no hanging parent php processes
  $thumbSize			= '1280x720';
  $ffmpegTimeout	= 'timeout 30'; // 30s timeout for exec
  
  function updateThumbs($ffmpegPath) 
  {
  	  $thumbSize			= '1280x720';
  	  $fireandforget  = '> /dev/null 2>/dev/null &' ; // add to exec (ffmpeg) call to make asynchronous (mmmm!) - no hanging parent php processes
  	  $ffmpegTimeout	= 'timeout 30'; // 30s timeout for exec
  for ($x = 1; $x <= 7; $x++)
   {
  	 // $ffmpegPath     = '/home/ubuntu/bin/';
     // $stream = 'aa-port'.$x.'-monitor';
     $stream = 'aa-port'.$x;
     if ($x==4) 
     {
      // dont do owt because port4 stream doesnt exist (stream names are mapped to SDI connectors)
     } else 
     {
     	// example command: timeout 30 /home/ubuntu/bin/ffmpeg -i 'rtmp://rtmp-qa1.projectapollo2.com:8935/livecams/aa-port1' -vframes 1 -s 1280x720 -vf select='eq(pict_type\,I),unsharp=5:5:0.5:5:5:0.0' -ss 1 -y -f image2 '/var/www/html/thumbs/aa-port1test-monitor.jpg' > /dev/null 2>/dev/null &
     	$fullString = $ffmpegTimeout.' '.$ffmpegPath."ffmpeg -i 'rtmp://rtmp-qa1.projectapollo2.com:8935/livecams/".$stream."' -vframes 1 -s ".$thumbSize." -vf select='eq(pict_type\,I),unsharp=5:5:0.5:5:5:0.0' -ss 1 -y -f image2 '/var/www/html/thumbs/".$stream."-monitor.jpg' ".$fireandforget;	
  		$success = exec($fullString,$miResult); // generate thumb from keyframe
     }
   }
  } 
    	
if (file_exists($rtmpjson)) 
	{
 	  $rtmpString = file_get_contents($rtmpjson);
    $rtmpArray = json_decode($rtmpString,true); 
    $oldTimestamp = $rtmpArray['timestamp'];
    $timeDiff = ($run_time - $oldTimestamp);
    
    if ($timeDiff>$thumbInterval)
    {
    	include ('/var/www/html/ajax/nuke.php'); // kill processes 
    	   	
    	updateThumbs($ffmpegPath);
    	$rtmpArray['timestamp'] = $run_time;
    	$rtmpString = json_encode($rtmpArray);
    	file_put_contents($rtmpjson, $rtmpString);
    
    }
    
    else 
    
    {
    
		// processCount();
   	
    }
  }



?>