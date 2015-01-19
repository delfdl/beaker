<?php 
     set_time_limit(0); // ffmpeg can take a long time to transcode a file :)
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $options = "a:b:c:";
     $opts = getopt($options);
     include ('/var/www/html/api/jonahlib.php');         // functions
     
     $logFile = $opts['a'];                     // logfile
     $ffmpegThumb = $opts['b'];                 
     $ffmpegCall = rawurldecode($opts['c']);    // ffmpeg call

     transcodeLog($logFile,'QC: starting reference encode ');
     $time = microtime();
     $time = explode(' ', $time);
     $time = $time[1] + $time[0];
     $start = $time;
 
     exec($ffmpegCall);                         // creates 60 second mp4
     
     $ffmpegThumb1 = $ffmpegThumb.'_thumb.jpg';
     exec($ffmpegThumb1);
     
     $ffmpegThumb2 = str_replace('160x90','640x360',$ffmpegThumb); 
     $ffmpegThumb3 = $ffmpegThumb2.'_poster.jpg';
     exec($ffmpegThumb3);
     
     
     
     // add in ffmpeg puke catching here, in case ffmpeg fails, then log error as 'ffmpeg puked' in transcodeLog()
     $time = microtime();
     $time = explode(' ', $time);
     $time = $time[1] + $time[0];
     $finish = $time;
     $total_time = round(($finish - $start), 4);
     $mins = intval($total_time/60);
     $secs = round($total_time-($mins*60),3);
     transcodeLog($logFile,'QC: completed reference encode Time Taken: '.$mins.'m '.$secs.'s');
 

?>