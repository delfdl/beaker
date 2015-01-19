<?php
     set_time_limit(0); // ffmpeg can take a long time to transcode a file :)
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $options = "a:b:c:";
     $opts = getopt($options);
     include ('/var/www/api/jonahlib.php');         // functions
     
     $logFile = $opts['a'];                     // logfile
     $ffmpegThumb = rawurldecode($opts['b']);                 
     $ffmpegCall = rawurldecode($opts['c']);    // ffmpeg call

     transcodeLog($logFile,'QC: starting reference encode ');
     $time = microtime();
     $time = explode(' ', $time);
     $time = $time[1] + $time[0];
     $start = $time;
     
     // readLog($logFile);
 
     exec($ffmpegCall);                         // creates 10 minute mp4
     transcodeLog($logFile,'QC: mp4 done, moving to thumbs ');
     // ffmpeg -i '/mount/wing/vod/otv/mtv/pacificrim0410/pacificrim0410.mp4' -vframes 1 -s 160x90 -vf select='eq(pict_type\,I)' -ss 60 -f image2 -y '/mount/wing/vod/otv/mtv/pacificrim0410/pacificrim0410_thumb.jpg'
     
     // grab 160x90 Thumb
     
     $ffmpegThumb1 = $ffmpegThumb."_thumb.jpg'";
     transcodeLog($logFile,'QC: thumbgen: '.$ffmpegThumb1);
     exec($ffmpegThumb1);
     
     $chopLeft = strstr($ffmpegThumb1,'-ss',true);
     $chopRight = strstr($ffmpegThumb1,'-f',false);  
            
     for ($loop = 1; $loop <= 5; $loop++) 
      {
            $leftStr = strstr($ffmpegThumb1,'-f',true);
            $bittoreplace  = strstr($leftStr,'-ss',false);
            $justtheValue  = str_replace('-ss ','',$bittoreplace);
            $extranewValue = intval($justtheValue*$loop);
            $newchopRight  = str_replace('thumb.','thumb'.$loop.'.',$chopRight);
            $tryThis = $chopLeft.' -ss '.$extranewValue.' '.$newchopRight;
            transcodeLog($logFile,'QC: debug '.$tryThis);
            exec($tryThis); // create thumb varients
            $delimiter = "'";
            $usefulbit = explode($delimiter,$tryThis);
            $dirPath = $usefulbit[1]; // should be the first full mp4 path
            $thumbPath = str_replace('.mp4','_thumb'.$loop.'.jpg',$dirPath);
            transcodeLog($logFile,'QC: analysing: '.$thumbPath);

                 // check thumb.jpg

            $rgbResult = rgbCount($thumbPath,$logFile);
            transcodeLog($logFile,'QC: RGB of thumb'.$loop.': '.$rgbResult);
     
            $rgbArray = explode(':',$rgbResult);
            $rgbTotal = $rgbArray[0]+$rgbArray[1]+$rgbArray[2];
            transcodeLog($logFile,'QC: RGB total'.$rgbTotal);
            if ($rgbTotal<75) // ie dark
            {
                // too dark, check next thumb
            } 
     } 
     
     $ffmpegThumb2 = str_replace('160x90','640x360',$ffmpegThumb); 
     $ffmpegThumb3 = $ffmpegThumb2."_poster.jpg'";
     transcodeLog($logFile,'QC: postergen: '.$ffmpegThumb3);
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