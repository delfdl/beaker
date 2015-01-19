<?php 
 /*How to check how many processors are available on the current machine.
 by gordon@incero.com http://www.Incero.com. Feel free to use this code,
 but keep this header. June 30th, 2010.*/
 $numberOfProcessors=`cat /proc/cpuinfo | grep processor | tail -1`;
 $numberOfProcessors=preg_replace('/s+/', '',$numberOfProcessors);
 $numberOfProcessors=str_replace(":","", $numberOfProcessors);
 $numberOfProcessors=str_replace("processor","", $numberOfProcessors);
 $numberOfProcessors++;
 echo "Number of processors on this machine is $numberOfProcessors!<br /><br />";
 ?>

<?php 
$commandtoUse = "ffmpeg -i '/home/secure/cat.shit.one.mp4' -c:v libx264 -b:v 600000 -s 640x360 -r 25 -g 100 -pix_fmt yuv420p -vf scale='640:266,pad=640:360:0:47:black' -bf 8 -coder 1 -vprofile main -level 3.1 -threads 0 -keyint_min 100 -sc_threshold 0 -t 300 -metadata description='reference transcode by Jonah'  -y /mount/wing/vod/otv/mtv/cputest/cputest64.mp4";
     $time = microtime();
     $time = explode(' ', $time);
     $time = $time[1] + $time[0];
     $start = $time;
exec($commandtoUse, $fred);
     $time = microtime();
     $time = explode(' ', $time);
     $time = $time[1] + $time[0];
     $finish = $time;
     $total_time = round(($finish - $start), 4);
     $mins = intval($total_time/60);
     $secs = round($total_time-($mins*60),3);
     echo ('time taken with threads auto: '.$mins.'m '.$secs.'s<br />');    
   
     
$commandtoUse = "ffmpeg -i '/home/secure/cat.shit.one.mp4' -c:v libx264 -b:v 600000 -s 640x360 -r 25 -g 100 -pix_fmt yuv420p -vf scale='640:266,pad=640:360:0:47:black' -bf 8 -coder 1 -vprofile main -level 3.1 -threads 128 -keyint_min 100 -sc_threshold 0 -t 300 -metadata description='reference transcode by Jonah'  -y /mount/wing/vod/otv/mtv/cputest/cputest64.mp4";
     $time = microtime();
     $time = explode(' ', $time);
     $time = $time[1] + $time[0];
     $start = $time;
exec($commandtoUse, $fred);
     $time = microtime();
     $time = explode(' ', $time);
     $time = $time[1] + $time[0];
     $finish = $time;
     $total_time = round(($finish - $start), 4);
     $mins = intval($total_time/60);
     $secs = round($total_time-($mins*60),3);
     echo ('time taken with threads 128: '.$mins.'m '.$secs.'s<br />'); 
              
$commandtoUse = "ffmpeg -i '/home/secure/cat.shit.one.mp4' -c:v libx264 -b:v 600000 -s 640x360 -r 25 -g 100 -pix_fmt yuv420p -vf scale='640:266,pad=640:360:0:47:black' -bf 8 -coder 1 -vprofile main -level 3.1 -threads 512 -keyint_min 100 -sc_threshold 0 -t 300 -metadata description='reference transcode by Jonah'  -y /mount/wing/vod/otv/mtv/cputest/cputest32.mp4";
     $time = microtime();
     $time = explode(' ', $time);
     $time = $time[1] + $time[0];
     $start = $time;
exec($commandtoUse, $fred);
     $time = microtime();
     $time = explode(' ', $time);
     $time = $time[1] + $time[0];
     $finish = $time;
     $total_time = round(($finish - $start), 4);
     $mins = intval($total_time/60);
     $secs = round($total_time-($mins*60),3);
     echo ('time taken with threads 512: '.$mins.'m '.$secs.'s<br />'); 
?>  
    