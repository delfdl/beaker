<?php
// s/w transcode using php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $audiocount=1; // assume 1 audio until detected (just to define the variable)
     
     $responseString = json_decode(file_get_contents('php://input'), true); // get json post and decode to array

     include ('/var/www/api/jonahlib.php');         // functions

     // identify src file, working tempname, organisation, hqflag (optional)
        $srcfile = $responseString['src'];
        $jobref  = $responseString['job'];
        $tmpname = $responseString['tmpname'];
        $org     = $responseString['org'];
        $channel = $responseString['channel'];
        $hqflag  = $responseString['hqflag'];
        $rewrap  = $responseString['rewrap'];
     
     // create job logfile using working tmpname
      $path = '/var/www/api/jobs/'.$org.'/'; // should already exist
      $logFile = $path.$jobref.'.log';
      transcodeLog($logFile,'received');
      
         if ($rewrap==1) 
         {
            $format='mp4';
            rewrap($srcfile,$tmpname,$logFile,$format);
            // echo ('rewrapped');
         }

     // analyse file
     $mi = getMediaInfo($srcfile);

        $execMI = 'mediainfo -p '.$srcfile;
        $miresult = array();
        $result = exec($execMI,$miresult);
        $incomingformat = $mi->get_general_property('Format');
        $formatversion = $mi->get_general_property('Format version');
        if ($formatversion=='Property does not exists') {$formatversion='';}
        $incomingformat = $incomingformat.' | '.$formatversion;
     
       // count audio tracks   // use ffprobe instead
       // foreach ($miresult as $result)
       // {
       //     if ((strpos($result,'udio')) && (strpos($result,'#')))  {$audiocount++;}
       //  }
         
        $tmpInfo = getProbeinfo($srcfile);
        $tmpJson = json_decode($tmpInfo,true);
        $audiocount = $tmpJson['audiocount'];
     
        // check resolution (SD or HD) to determine number of video profiles
         $width = $mi->get_video_property('Width');
         $actualWidth=intval(str_replace(' ','',$width)); // get src width (HD>1024)
         
         // determine preset to use
        // if ($actualWidth>1024) {$encRes='hd';} else {$encRes='sd';}
        if ($actualWidth<1024) {$encRes='sd';} else 
        { 
            if ($actualWidth>2048)  
            {
                $encRes='uhd';
            } 
            else 
            {
                $encRes='hd';
            }         
        }

     
     $presettoUse = '/home/presets/'.$org.$encRes.'.preset'; 
     transcodeLog($logFile,'actualWidth = '.$actualWidth.', therefore using '.$encRes.' preset: '.$presettoUse);      
   
     // load preset
    $explodeID = '*';
    $i=0;
    if (file_exists($presettoUse)) 
    {
        $fullPreset = file_get_contents($presettoUse);                 //     
        $presetExplode = explode($explodeID,$fullPreset);
    }
    else
    {
        transcodeLog($logFile,$presettoUse.' not found');    
    }

    //echo ("fullPreset: ".$fullPreset."<br />"); 
    
    // parse preset
    foreach($presetExplode as $presetPass)
    {    
        if (!$presetPass) 
        {
        // dont show before *
        } 
        else 
        {
             
            $preset = json_decode($presetPass, true);
            if (($preset['type']=='video') || ($preset['type']=='reference'))
            {
                $i++ ; 
                // echo 'video found'.$i."\n";
            }
             // count number of profiles (how to deduct audio profiles?)
             $numVidProfiles = $i;
        }     
    }  

     // calculate number of steps required to complete process
   switch($hqflag)
     {
        case 0:
            $numberofvideoPasses=$numVidProfiles;
            break;
        case 1:
            $numberofvideoPasses=$numVidProfiles+1;
            break;
        case 2:    
            $numberofvideoPasses=$numVidProfiles*2;
            break;
     }
    
    transcodeLog($logFile,'audiocount pre stageCount :'.$audiocount); // this is +1 too high
    if ($audiocount>0) {$numberofaudioPasses = $audiocount+1;} else {$numberofaudioPasses = 0;}
    // $numberofaudioPasses = $audiocount+1; // one pass for every audio track @96k, plus 1 @ 56k
    echo " | audio: ".$numberofaudioPasses." video:".$numberofvideoPasses." | \n ";
    $stageCount = $numberofaudioPasses+$numberofvideoPasses;
    $perCent = intval(100/$stageCount);

    // record start timestamp
    $tmp1 = getProbeinfo($srcfile);
   // echo $tmp1;
   transcodeLog($logFile,'ffprobe summary tmp1 - '.$tmp1); 
   $tmpJson = json_decode($tmp1,true);
    //echo $tmpJson;
    $tmp2 = $tmpJson['container']." (".$tmpJson['streamcount']." streams) | VIDEO id:1 ". $tmpJson['width']."x".$tmpJson['height']." ".$tmpJson['videocodec'].", ".intval($tmpJson['duration'])."secs, ".$tmpJson['fps']."fps | ";
    $audiocounter = $tmpJson['audiocount'];
    transcodeLog($logFile,'ffprobe audiocount - '.$tmpJson['audiocount']); 
    
    if (($audiocount==0) && ($audiocounter>0)) 
    {
        transcodeLog($logFile,'audiocount :'.$audiocount.', audiocounter :'.$audiocounter); // 
        $audiocount=$audiocounter;
        $numberofaudioPasses=$audiocount+1;
        transcodeLog($logFile,'audiocount :'.$audiocount);
    } // checks to see if mediainfo audiocount of 0 is reconciled with zero count of ffprobe. if not, uses the ffprobe count. 

    if (isset($tmpJson['1-channels']))              // some containers dont populate this
    {
        for ($x=1; $x<=$audiocounter; $x++)
        {     
                switch($tmpJson[$x.'-channels'])
                {
                   case 1: 
                        $ffchannels = 'mono';
                        break; 
                   case 2: 
                        $ffchannels = 'stereo';
                        break; 
                   case 3: 
                        $ffchannels = '2.1';
                        break; 
                   case 4: 
                        $ffchannels = 'quadrophonic';
                        break;
                   case 6: 
                        $ffchannels = '5.1 surround';
                        break; 
                   case 8: 
                        $ffchannels = '7.1 surround';
                        break;    
                 }
                
            $tmp2 = $tmp2."AUDIO id:".$tmpJson[$x.'-audiomap']." ".$tmpJson[$x.'-audiocodec']." ".$ffchannels." ".$tmpJson[$x.'-language']." | ";
        }
    }    
    echo $tmp2;
    
    transcodeLog($logFile,'ffprobe summary tmp2 - '.$tmp2); 
    transcodeLog($logFile,'analysed - '.$stageCount.' passes ['.$numVidProfiles.' profiles | '.$tmpJson['audiocount'].' audio tracks | HQ='.$hqflag.']'); // zero going in here for some reason?
         
     
     // map first video stream // ffmpeg does as default

        // for each profile in preset
        // create ismv, no audio according to video profile
        // update progress
        
   $i = 0; // reset stage counter
   $a = 0; // audio count
       
   foreach($presetExplode as $presetPass)
    {    
        if (!$presetPass) 
        {
        // dont show before *
        } 
        else 
        {             
            // do first passes only 
            $preset = json_decode($presetPass, true);
            $i++; $a++;
            
            if ($preset['type']=='video')
            {
              transcodeLog($logFile,'video preset '.$i);   
              transcodeVideo($preset,$srcfile,$org,$channel,$hqflag,$logFile,$tmpname,$i);
            }
            
            if ($preset['type']=='audio')
            {
              transcodeLog($logFile,'audio preset '.$i);   
              transcodeAudio($preset,$srcfile,$org,$channel,$hqflag,$logFile,$tmpname);    
            }
            
           if ($preset['type']=='subtitle')
            {
              transcodeLog($logFile,'subtitle preset '.$i);  
              transcodeTitle($preset,$srcfile,$org,$channel,$hqflag,$logFile,$tmpname);    
            }
            
            if ($preset['type']=='reference')
            {
              transcodeLog($logFile,'QC reference '.$i);   
              transcodeReference($preset,$srcfile,$org,$channel,$hqflag,$logFile,$tmpname,$i);
            }            
            

           //  $completion = $i * $perCent ;
           //  transcodeLog($logfile,'pass complete '.$completion);
        }
    }
        
       
        // QA on output folder
        // record finish timestamp
        
        // log completed job     
?>