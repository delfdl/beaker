<?php
	$startTime = date("U");
    ini_set ("display_errors", "1");
    error_reporting(E_ALL);
	$src  = $_GET["src"]; 
	$hash = $_GET["hash"];

echo 'Verifying... '.$src; // 	eg /home/secure/ott134_1285b49ec772a58a7407b17eb01fba02.mp4
if (file_exists($src))
{
    echo(' - file exists - OK');
    $dlm = date ("F d Y H:i", filemtime($src));
    $fSize = intval(filesize($src)/1024/1024);
    echo (', modified: '.$dlm.' | filesize: '.$fSize.' Mb<br />');
    ;}
else 
{echo(' - file not found!<br />');}



function thumbGen($inputVOD,$thumbTime,$offset,$outURL)
 	{  
 		$selectI =" -vf select='eq(pict_type\,I)'"; // use filter to select nearest keyframe
 		// $selectI = "";
		// $fireandforget = "> /dev/null 2>/dev/null &" ; // add to exec (ffmpeg) call to make asynchronous (mmmm!)
		$fireandforget = '';
 		// $ffmpegThumb = "ffmpeg -itsoffset -".$offset." -i '".$inputVOD."' -vframes 1 -s 320x180 ".$selectI." -ss ".$thumbTime." -f image2 '".$outURL."'".$fireandforget; // kick off thumbgen
        $ffmpegThumb = "ffmpeg -i '".$inputVOD."' -vframes 1 -s 320x180 ".$selectI." -ss ".$thumbTime." -f image2 '".$outURL."'".$fireandforget; // kick off thumbgen
		echo "Executing: <br /><i>". $ffmpegThumb."</i><br /><br />" ;
		//echo ("ffmpegThumb: ".$ffmpegThumb."<br />");
		$tmp = exec($ffmpegThumb);
		//echo $tmp."<br />";
		//if (!exec($ffmpegThumb))
		//	{$success=0;} 
		//else 
		//	{$success=1;}
		//	echo ("success: ".$success."<br />");
    return $tmp;
 	}
    
function getDuration($inputVOD)
    {
    $durCommand = "ffmpeg -i ".$inputVOD." 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//";
    $tmp = exec($durCommand);
    if (!$tmp) {$tmp='0:0:9';}          // if 'ffmpeg -i' fails
    echo "Duration: ".$tmp."<br />";
        $tmpDur = explode(":",$tmp);
        $tmp1   = $tmpDur[0] * 360;
        $tmp2   = $tmpDur[1] * 60;
        $secDuration = intval($tmp1 + $tmp2 + $tmpDur[2]);
        echo ('Total seconds: '.$secDuration.'<br />');
    
    return $secDuration;    
    }
    
		$outURL = '/var/www/html/thumbs/'.$hash.'post.jpg';
		$inputVOD = $src;
		$thumbTime = '60';
		$res = '320x180'; // unused
		$offset='100';

        $duration = getDuration($inputVOD); 
        echo ('Duration of file '.$inputVOD.': '.$duration.' s<br />'); 
        
        if ($duration>600) {$thumbTime=600;} else {$thumbTime=intval($duration/2);} // take thumbnail in the middle unless video>10 mins, then take it at 10min point
        
		$success = thumbGen($inputVOD,$thumbTime,$offset,$outURL);
		$finalTime=date("U");
		echo $success."<br /><br />"."<img src='http://ingest2.ot2.tv/thumbs/".$hash."post.jpg' /><br /><br />";

		$timeTaken = ($finalTime - $startTime);
		echo ("Time taken: ".$timeTaken);

?>