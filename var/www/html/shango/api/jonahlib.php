<?php
require_once('class.mediaInfo.php');            // third party mediainfo class

function transcodeLog($logfile,$statement) 
    {
      $memInfo = '/proc/meminfo';
      $mem_array = file($memInfo);
      foreach ($mem_array as $mem_key)
      {
        // echo ("<br />".$mem_key);
        if (strpos($mem_key,"emTotal")) 
             {  
                $cerb_MT = str_replace("MemTotal: ","",$mem_key);
                $cerb_MT = (str_replace(" kB","",$cerb_MT)) ;
                $cerb_MT = round($cerb_MT/1024/1024,3); // memory in Gb to 3 decimal places
                //echo "cerb_mt: ".$cerb_MT."<br />";
                $memInstalled = ceil($cerb_MT); 
                //echo ("cerb_mi: ".$memInstalled." Gb found <br />");
             }
             
         if (strpos($mem_key,"emFree")) 
             {  
                $cerb_MF = str_replace("MemFree: ","",$mem_key);
                $cerb_MF = (str_replace(" kB","",$cerb_MF)) ;
                $cerb_MF = round($cerb_MF/1024/1024,3); // memory in Gb to 3 decimal places
                //echo "cerb_mf: ".$cerb_MF."<br />";
             }    
      }

      $memPercent = ceil(100-(($cerb_MF/$cerb_MT)*100)); // get percent memFree, remove from 100 to get mem used
        
      $handle = fopen($logfile, 'a');
      $time = date('d/m/y H.i:s');
      $load = sys_getloadavg();
      $cpu = 'cpu: '.$load[0].'% ';
      $mem = 'mem: '.$memPercent.'% ';
      $data = "\n[".$time.'] '.$statement.' ('.$cpu.' | '.$mem.')';
      fwrite($handle, $data);
      fclose($handle); 
    }
     
function getMediaInfo($filepath) 
    {
        $mi = new mediaInfo($filepath);
        return $mi;
    }
    
function getProbeinfo($src)
    {
    $srt = urlencode($src);
    $url = 'http://ingest2.ot2.tv/api/filesummary.php?src='.$src; 
    $response = file_get_contents($url);
    return $response; 
    }      

function getAR($srcfile)
    {
        $ffprobe_short = getProbeinfo($srcfile);
        $aspectJson = json_decode($ffprobe_short,true);
        $aspect = $aspectJson['dar'];
        $workingthisout = explode(':',$aspect);
        if (isset($workingthisout[1]))
        {   if ($workingthisout[1]==0) {$workingthisout[1]=1;} // avoid division by 0 errors
            $correctaspect = $workingthisout[0]/$workingthisout[1]; // division by 0 error here occasionally, undefined offset too.
        } else
        {
            $tmpWidth = $aspectJson['width'];
            $wFactor = $tmpWidth/16; 
            $tmpHeight = $aspectJson['height'];
            $hFactor = $tmpHeight/$wFactor;
            $aspect = '16:'.intval($hFactor); // 16:9
            $correctaspect = 16/intval($hFactor);
        }
       if (($correctaspect<1) || ($correctaspect>3)) {$aspect = $aspectJson['width'].':'.$aspectJson['height'];}
       
       if ($aspect==':') {$aspect='16:9';} // last gasp check to make sure at least some aspect ratio is returned, even if its wrong 
        return $aspect;    
    }
    

function ffmpegExt($ffmpegCall, $logFile, $i) 
    {
    $ffmpegCall = str_replace("  "," ",$ffmpegCall);
    $ffmpegCall = rawurlencode($ffmpegCall);
    $fullremote = "php -f /var/www/api/ffmpegcall.php -- -a ".$logFile." -b ".$i." -c ".$ffmpegCall." > /dev/null 2>/dev/null &" ;  // make async call to ffmpeg controller (not ffmpeg itself)
    // transcodeLog($logFile,$fullremote); 
    exec($fullremote);
    }
    
   
function ffmpegExtSynch($ffmpegCall, $logFile, $ffmpegThumb) 
    {
    $ffmpegCall = str_replace("  "," ",$ffmpegCall);
    $ffmpegCall = rawurlencode($ffmpegCall);
    $ffmpegThumb = rawurlencode($ffmpegThumb);
    
    $fullremote = "php -f /var/www/api/ffmpegcallsync.php -- -a ".$logFile." -b ".$ffmpegThumb." -c ".$ffmpegCall." > /dev/null 2>/dev/null &" ;  // make async call to ffmpeg controller (not ffmpeg itself)
    transcodeLog($logFile,'ffmpegExtSynch as '.$fullremote); 
    exec($fullremote);
    }

function rewrap($srcfile,$tmpname,$logFile,$format) 
    {
        // count audio tracks
        // analyse file
     $mi = getMediaInfo($srcfile);
     if ($mi[0]=='unable to validate')
     {
         transcodeLog($logFile,$mi[0]);
     }
  //   $aspect = ' -aspect '.getAR($srcfile).' ';  // dont need as copying video stream #0
     $execMI = 'mediainfo -p '.$srcfile;
     $miresult = array();
     $result = exec($execMI,$miresult);
     $audiocount = 0;
     $audiooptions = '';
     foreach ($miresult as $result)
       {
         if ((strpos($result,'udio')) && (strpos($result,'#')))  
            {
             $audiocount++;
             $trackno = $audiocount;
             // try and identify language
             $audiooptions = $audiooptions.' -map 0:'.$trackno.' -c:a:'.$trackno.' libfdk_aac -b:a:'.$trackno.' 96k ';
            }
       }
        // ffmpeg -i $srcfile -c copy -c:v copy $audiooptions $tmpname_rewrap.mp4

       $suffix = $format; 
       $fireandforget = "> /dev/null 2>/dev/null &" ; // add to exec (ffmpeg) call to make asynchronous (mmmm!)
       $outputdir = '/home/secure/rewrap/'.$tmpname;
       
       if(!file_exists($outputdir))    
        {
            transcodeLog($logFile,'creating rewrap output path - '.$outputdir);
            mkdir($outputdir, 0777, true); 
        }
       
       $ffmpegRewrap = "ffmpeg -y -i '".$srcfile."' -c copy ".$audiooptions." ".$outputdir."/".$tmpname."_rewrap.".$suffix." ".$fireandforget;
       transcodeLog($logFile,'starting rewrap ['.$ffmpegRewrap.']'); 
       exec($ffmpegRewrap);
       transcodeLog($logFile,'completed rewrap');
    }

function getScaling($srcfile,$screenres) 
{
  $scaling      = '';
  $colour       = 'black'; // define padding colour, defaults to black
  $basicInfo    = getProbeinfo($srcfile);
  $detailsJson  = json_decode($basicInfo,true);
  $width        = $detailsJson['width'];
  $height       = $detailsJson['height']; // add additional error trapping incase height/width cant be detected?
  $workingRatio = $width/$height;         // input aspect ratio, not counting for anamorphic (PAR)
  $outputRes    = explode('x',$screenres);
  $outputWidth  = $outputRes[0];
  
  // check for anamorphic
  $dar          = $detailsJson['dar'];       // not always detected, depending upon container
  $darArray     = explode(':',$dar);
  $xaxis        = $darArray[0]; 
  if (isset($darArray[1])) 
    {
        $yaxis = $darArray[1];
        $darRatio = $xaxis/$yaxis;
        if ($darRatio>$workingRatio) // input file is anamorphic?
        {
           $workingRatio = $darRatio;   // recalculate
        }
    } 
    else 
    {
        // no need to do any fancy DAR calculation if its not explicitally stated
    }
    
  if (isset($outputRes[1])) {$outputHeight=$outputRes[1];} // should always be set unless preset is corrupt
  
  $newWidth = $outputWidth;     // setting up for scaling, pre-adjustment
  $newHeight = $outputHeight; 
  
  if ($workingRatio>1.78) // ie letterboxed content
  {
      $newHeight = intval($outputWidth/$workingRatio);
      $padding   = intval((($outputHeight-$newHeight)/2));
      $scaling   = " -vf scale='".$outputWidth.":".$newHeight.",pad=".$outputWidth.":".$outputHeight.":0:".$padding.":".$colour."' ";
  } 
  else
  {
      $newWidth = intval($outputHeight*$workingRatio);
      $padding  = intval((($outputWidth-$newWidth)/2));
      $scaling  = " -vf scale='".$newWidth.":".$outputHeight.",pad=".$outputWidth.":".$outputHeight.":".$padding.":0:".$colour."' ";    
  }
  
 // if (($evalWorking==1.78) && ($evalDar==1.78)) // if non-anamorphic src is 16:9, skip scaling to improve render performance
 if (($newWidth==$outputWidth) && ($newHeight==$outputHeight)) 
  {
   $scaling = ' '; // bypass scaling  
  }
  
  return $scaling;
}
    
function transcodeVideo($preset,$srcfile,$org,$channel,$hqflag,$logFile,$tmpname,$i)
    {
      $codecvideo = $preset['-c:v'];
      $framerate = $preset['-r'];
      $bitratevideo = $preset['-b:v'];
      $bitratevideo = str_replace('k','000',$bitratevideo);
      $screenres = $preset['-s'];
      $gopsize = $preset['-g'];
      $addition1 = $preset['addition1']; 
      $avifix = ' -pix_fmt yuv420p'.' ';
      $scaling = getScaling($srcfile,$screenres);
      $part1=' -c:v '.$codecvideo.' -b:v '.$bitratevideo.' -s '.$screenres.' -r '.$framerate.' -g '.$gopsize.' '.$avifix.$scaling.$addition1;
      $part2=' -an -y ';  
      $inputvod = $srcfile;
      $suffix = $preset['container'];
      $aspect = ' -aspect '.getAR($srcfile).' ';
      
      // $fireandforget = "> /dev/null 2>/dev/null &" ; // add to exec (ffmpeg) call to make asynchronous (mmmm!)
      $fireandforget = '';
      $fragment = '-movflags frag_keyframe ';
      $metaJonah = " -metadata description='video transcode by Jonah'";
      $outputdir = '/mount/wing/vod/'.$org.'/'.$channel.'/'.$tmpname.'/';
      
      if(!file_exists($outputdir))    
        {
            mkdir($outputdir, 0777, true);
            transcodeLog($logFile,'creating output video path - '.$outputdir); 
        }
                
      $outputvod = $outputdir.$tmpname.'_20'.$i.'_'.$bitratevideo.'.'.$suffix;
      $ffmpegCall = "ffmpeg -i '".$inputvod."' ".$part1." ".$metaJonah." ".$part2." ".$fragment." ".$aspect." ".$outputvod." ".$fireandforget;
      transcodeLog($logFile,"debug - ".$ffmpegCall."\n"); 
   
      ffmpegExt($ffmpegCall, $logFile, $i);

    }
    
function transcodeAudio($preset,$srcfile,$org,$channel,$hqflag,$logFile,$tmpname)  
    {
      $a=0;
      $codecaudio = $preset['-c:a'];
      $bitrateaudio = $preset['-b:a'];
      $bitrateaudio = str_replace('k','000',$bitrateaudio);
      $samplerate = $preset['-ar'];
      $channels = $preset['-ac'];
      $addition2 = $preset['addition2']; 
      $inputvod = $srcfile;
      $suffix = $preset['container'];
      $fireandforget = "> /dev/null 2>/dev/null &" ; // add to exec (ffmpeg) call to make asynchronous (mmmm!)
      // $fireandforget = '';
      $audiolang = 'eng';
      $fragment = ' -movflags frag_keyframe ';
      $metaJonah = " -metadata description='audio transcode by Jonah' ";
      $outputdir = '/mount/wing/vod/'.$org.'/'.$channel.'/'.$tmpname.'/';
      
      if(!file_exists($outputdir))    
        {
            mkdir($outputdir, 0777, true);
            transcodeLog($logFile,'creating audio output path - '.$outputdir); 
        }
        
      // start paste
      
     // analyse file
     $mi = getMediaInfo($srcfile);
     $execMI = 'mediainfo -p '.$srcfile;
     $miresult = array();
     $result = exec($execMI,$miresult);
     $audiocount = 0;
     $audiooptions = '';
     
     $ffprobe_short = getProbeinfo($srcfile);
     $detailsJson = json_decode($ffprobe_short,true);
     $howmanyAudio = $detailsJson['audiocount'];
     
     for ($audioTrack = 1; $audioTrack <= $howmanyAudio; $audioTrack++) 
     {
             $identifymap = $audioTrack.'-audiomap';
             $trackno = $detailsJson[$identifymap]; // use audio track mapping where video isnt track 0;
             
             $identifylang = $audioTrack.'-language'; 
             $infolang = $detailsJson[$identifylang]; // find language ISO code, which defaults to 'und'
             
             // try and identify language and put into $infolang
             if (isset($infolang)) {$audiolang=$infolang;} else {$audiolang='und';} 
             
             $audiooptions = ' -map 0:'.$trackno.' -c:a:'.$trackno.' '.$codecaudio.' -b:a:'.$trackno.' '.$bitrateaudio.' -ar '.$samplerate.' -ac '.$channels.' -metadata:s:a:'.$trackno.' language='.$audiolang.' ';
             $appleoptions = ' -map 0:'.$trackno.' -c:a:'.$trackno.' '.$codecaudio.' -b:a:'.$trackno.' 56000 -ar '.$samplerate.' -ac 2 '.'-metadata:s:a:'.$trackno.' language='.$audiolang.' ';
             $outputvod = $outputdir.$tmpname.'_10'.$audioTrack.'_'.$bitrateaudio.'.'.$suffix;
             $outputapple = $outputdir.$tmpname.'_10'.$audioTrack.'_56000'.'.'.$suffix;
             $ffmpegAudio = "ffmpeg -i '".$srcfile."' -vn ".$audiooptions." ".$addition2." ".$outputvod." ".$fireandforget;
             $ffmpegApple = "ffmpeg -i '".$srcfile."' -vn ".$appleoptions." ".$addition2." ".$outputapple." ".$fireandforget;
             
             transcodeLog($logFile,'identified initial mapping '.$trackno.' as '.$infolang); 
             ffmpegExt($ffmpegAudio, $logFile, $audioTrack);
              
             if ($audioTrack==1) 
                {
                    transcodeLog($logFile,'starting (applestore variant) audio '.$audioTrack.'['.$ffmpegApple.']'); 
                    exec($ffmpegApple);
                    transcodeLog($logFile,'completed (applestore variant) audio '.$audioTrack);   
                 }
      }
             
    } 
    
function transcodeSubtitle($preset,$srcfile,$org,$channel,$hqflag,$logFile,$tmpname,$i)
    {
    //  not ready
    $ffprobe_short = getProbeinfo($srcfile);
    $detailsJson = json_decode($ffprobe_short,true);
    }    
    
    
function createIsm($tmpname,$path) 
    {
    $srt = urlencode($tmpname);
    $url = 'http://live.ot2.tv/tools/api/mp4splitremote.php?tmpname='.$tmpname.'&path='.$path; 
    $ch = curl_init(); 
    // echo ("initiating ".$url."<br />");
    $timeout = 5; 
    curl_setopt($ch,CURLOPT_URL,$url); 
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
    $response = curl_exec($ch); 
    curl_close($ch);         
    }
    
function genMultipleThumbs($ffmpegThumb1) 
    {
       $chopLeft = strstr($ffmpegThumb1,'-ss',true);
       $chopRight = strstr($ffmpegThumb1,'-f',false);  
            
       for ($i = 1; $i <= 5; $i++) 
      {
            $leftStr = strstr($ffmpegThumb1,'-f',true);
            $bittoreplace = strstr($leftStr,'-ss',false);
            $justtheValue = str_replace('-ss ','',$bittoreplace);
            $extranewValue = intval($justtheValue*$i);
            $newchopRight = str_replace('thumb.','thumb'.$i.'.',$chopRight);
            $tryThis = $chopLeft.' -ss '.$extranewValue.' '.$newchopRight;
            // echo $tryThis.'<br />';
       }
    }    
    
function transcodeReference($preset,$srcfile,$org,$channel,$hqflag,$logFile,$tmpname,$i)
    {
      $codecvideo = $preset['-c:v'];
      $framerate = $preset['-r'];
      $bitratevideo = $preset['-b:v'];
      $bitratevideo = str_replace('k','000',$bitratevideo);
      $screenres = $preset['-s'];
      $gopsize = $preset['-g'];
      $addition1 = $preset['addition1']; 
      $avifix = ' -pix_fmt yuv420p ';
      $scaling = getScaling($srcfile,$screenres);
      $part1 = ' -c:v '.$codecvideo.' -b:v '.$bitratevideo.' -s '.$screenres.' -r '.$framerate.' -g '.$gopsize.' '.$avifix.$scaling.$addition1.' -t 600'; // first 10 minutes only
      // $part2=' -an -y ';
      $part2 = '';  
      $inputvod = $srcfile;
      $suffix = $preset['container'];
      $aspect = ' -aspect '.getAR($srcfile).' ';
      
      $fireandforget = '';
      $fragment = ' ';
      $metaJonah = " -metadata description='reference transcode by Jonah™'";
      $outputdir = '/mount/wing/vod/'.$org.'/'.$channel.'/'.$tmpname.'/';
      
      if(!file_exists($outputdir))    
        {
            mkdir($outputdir, 0777, true);
            transcodeLog($logFile,'creating output video path - '.$outputdir); 
        }
                
      $outputvod = $outputdir.$tmpname.'.'.$suffix;
      $ffmpegCall = "ffmpeg -i '".$inputvod."' ".$part1." ".$metaJonah." ".$part2." ".$fragment." ".$aspect." ".$outputvod." ";
      transcodeLog($logFile,"transcodeReference - ".$ffmpegCall."\n"); 
      
      $ffprobe_short = getProbeinfo($srcfile);
      $durationJson = json_decode($ffprobe_short,true);
      
      if (isset($durationJson['duration']))    
      {
          $duration = $durationJson['duration']; // assume seconds
          transcodeLog($logFile,"duration - ".$duration."\n");
          if (($duration<>0) && (intval($duration)<360))
          {
            $thumbTime = intval($duration/5); // start taking thumbnails 20% of the way into the video
          }
          else {$thumbTime = '55';} // default to 30 if value of duration is 0
      }
      else
      {
          $thumbTime = '45';    // default to 60s if unable to determine duration
      }
      if (intval($thumbTime)<1) 
      {
          $thumbTime = '35';    // default to 25s if previous catchalls fail
      }

      $ffmpegThumb = "ffmpeg -i '".$outputvod."' -vframes 1 -s 160x90 -metadata description='thumb by Jonah' -vf select='eq(pict_type\,I)' -ss ".$thumbTime." -f image2 -y '".$outputdir.$tmpname;
      transcodeLog($logFile,"ffmpegThumb - ".$ffmpegThumb."\n");
      ffmpegExtSynch($ffmpegCall, $logFile, $ffmpegThumb);
   
    }
    
function rgbCount($imgUrl,$logFile) 
    {
    $im = imagecreatefromjpeg($imgUrl);
    for ($x=0;$x<imagesx($im);$x++) 
        {
                 for ($y=0;$y<imagesy($im);$y++) 
                 {
                         $rgb = imagecolorat($im,$x,$y);
                         $r   = ($rgb >> 16) & 0xFF;
                         $g   = ($rgb >>  8) & 0xFF;
                         $b   = $rgb & 0xFF;
                         $rTotal += $r;
                         $gTotal += $g;
                         $bTotal += $b;
                         $total++;
                 }
         }
    $rAverage = round($rTotal/$total);
    $gAverage = round($gTotal/$total);
    $bAverage = round($bTotal/$total);
//return $rAverage,$gAverage,$bAverage;
    transcodeLog($logFile,"rgbCount analysis of ".$imgUrl." - r".$rAverage." g".$gAverage." b".$bAverage."\n"); 
    return $rAverage.":".$gAverage.":".$bAverage;
    }    
      
?>