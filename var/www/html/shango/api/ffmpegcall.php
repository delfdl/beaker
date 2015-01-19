<?php
     set_time_limit(0); // ffmpeg can take a long time to transcode a file :)
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $options = "a:b:c:";
     $opts = getopt($options);
     $vodmountpath = '/mount/wing/vod';
     include ('/var/www/api/jonahlib.php');         // functions
     
     $logFile = $opts['a'];
     $i = $opts['b'];
     $ffmpegCall = rawurldecode($opts['c']);
     
     if (strpos($ffmpegCall,' -an ')) {$type='video';} else {$type='audio';}

     transcodeLog($logFile,'starting '.$type.' encode '.$i);
     $time = microtime();
     $time = explode(' ', $time);
     $time = $time[1] + $time[0];
     $start = $time;
 
     exec($ffmpegCall);
     // add in ffmpeg puke catching here, in case ffmpeg fails, then log error as 'ffmpeg puked' in transcodeLog()
     $time = microtime();
     $time = explode(' ', $time);
     $time = $time[1] + $time[0];
     $finish = $time;
     $total_time = round(($finish - $start), 4);
     $mins = intval($total_time/60);
     $secs = round($total_time-($mins*60),3);
     transcodeLog($logFile,'completed '.$type.' encode '.$i.' Time Taken: '.$mins.'m '.$secs.'s');

     $truePath1 = substr($ffmpegCall,strpos($ffmpegCall,$vodmountpath)); // ie becomes /mount/wing/vod/org/channel/name/
     $truePathArray = explode('/',$truePath1);
     $org = $truePathArray[4];
     $channel = $truePathArray[5];
     $tmpname = $truePathArray[6];
     // transcodeLog($logFile,'creating ism in /'.$org.'/'.$channel.'/'.$tmpname.'/');
     
     // make ism call
     $url = 'http://live.ot2.tv/tools/api/mp4splitremote.php?org='.$org.'&channel='.$channel.'&tmpname='.$tmpname; 
     $ch = curl_init(); 
     // echo ("initiating ".$url."<br />");
     $timeout = 5; 
     curl_setopt($ch,CURLOPT_URL,$url); 
     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
     curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
     $response = curl_exec($ch); 
     curl_close($ch); 
     
     

?>