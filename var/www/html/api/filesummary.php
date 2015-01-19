<?php 
// s/w transcode using php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $time = date('ymd-Hi');
     
// receive job parameters (src)
    
      if (isset($_GET["src"])) {$src = $_GET["src"];} 
        else {$src = 'invalid';}                                            // fail if not a valid path
        
      if (isset($_GET["debug"])) {$debug = $_GET["debug"];} 
        else {$debug = '0';}                                            // fail if not a valid path
        
if (!file_exists($src)) 
    {
        // 
    } 
    else
    {
        $summaryJson = array();
        $path_info = pathinfo($src);
        $summaryJson['container'] = $path_info['extension']; // get file extension
        $ffprobeExec = 'ffprobe -v quiet -print_format json -show_format -show_streams '.$src;

        $exec = exec($ffprobeExec,$ffprobeJson);
        $littleStr='';
        
        foreach ($ffprobeJson as $littletest) 
        {
        $littleStr=$littleStr.$littletest;  
        }
        
         if ($debug==1)
            {
             echo $littleStr.'<br /><br />';
            }
      }
        
       // echo "\nffprobe response: ".$littleStr."\n";
        $ffprobe = json_decode($littleStr, true);
        
        $videoneedle = '"codec_type": "video"';
        $audioneedle = '"codec_type": "audio"';
        $subtitleneedle = '"codec_type": "subtitle"';
        $videotypecount = substr_count($littleStr,$videoneedle);
        $audiotypecount = substr_count($littleStr,$audioneedle);
        $subtitletypecount = substr_count($littleStr,$subtitleneedle);
        
        // filename
        $summaryJson['filename']    = $ffprobe['format']['filename'];
        $summaryJson['videocount']    = $videotypecount;
        $summaryJson['audiocount']    = $audiotypecount;
        $summaryJson['subtitlecount'] = $subtitletypecount;
        $summaryJson['streamcount'] = $ffprobe['format']['nb_streams']; // wrong, will include text streams too (doh!) //

        $streamcount = intval($summaryJson['streamcount'])-1 ; // streams

        for ($x=0; $x<=$audiotypecount; $x++)
        {
            if ($ffprobe['streams'][$x]['codec_type']=='video')
            {
               $summaryJson['videomap']     = $ffprobe['streams'][$x]['index'];
               $summaryJson['width']        = $ffprobe['streams'][$x]['width'];
               $summaryJson['height']       = $ffprobe['streams'][$x]['height'];
               
               if (isset($ffprobe['streams'][$x]['profile'])) 
               {
               $summaryJson['videocodec']   = $ffprobe['streams'][$x]['codec_name'].' '.$ffprobe['streams'][$x]['profile'];
               } 
               else 
               {
               $summaryJson['videocodec']   = $ffprobe['streams'][$x]['codec_name'];    
               }
              
               if (isset($ffprobe['streams'][$x]['duration']))
                {
                    $summaryJson['duration'] = $ffprobe['streams'][$x]['duration'];
                } 
                else
               {
                   if (isset($ffprobe['format']['duration']))
                   {
                   $summaryJson['duration'] = $ffprobe['format']['duration'];
                   }
                   else 
                   {
                     $summaryJson['duration'] = 'unknown';  
                   }
               } 
               
               if (isset($ffprobe['streams'][$x]['bit_rate']))
                {
                    $summaryJson['videobitrate'] = $ffprobe['streams'][$x]['bit_rate'];
                } 
                else
               {
                   if (isset($ffprobe['format']['bit_rate']))
                   {
                   $summaryJson['videobitrate'] = $ffprobe['format']['bit_rate'];
                   }
                   else 
                   {
                     $summaryJson['videobitrate'] = 'unknown';  
                   }
               } 

               $fps = explode('/',$ffprobe['streams'][$x]['r_frame_rate']);
               $summaryJson['fps']          = round($fps[0]/$fps[1],2);
               //$summaryJson['fps'] = intval($summaryJson['fps']);
               $summaryJson['dar']          = $ffprobe['streams'][$x]['display_aspect_ratio'];
               if ($summaryJson['dar']='0:1')
               {$summaryJson['dar'] = $summaryJson['width'].':'.$summaryJson['height'];}
               
            }
            
             if ($ffprobe['streams'][$x]['codec_type']=='audio')
            {
               $summaryJson[$x.'-audiomap']   = $ffprobe['streams'][$x]['index'];
               $summaryJson[$x.'-audiocodec'] = $ffprobe['streams'][$x]['codec_name'];
               $summaryJson[$x.'-channels']   = $ffprobe['streams'][$x]['channels'];
               $summaryJson[$x.'-samplerate'] = $ffprobe['streams'][$x]['sample_rate'];
               
               if (isset($ffprobe['streams'][$x]['bit_rate'])) 
               {
               $summaryJson[$x.'-bitrate']    = $ffprobe['streams'][$x]['bit_rate'];
               }
               else 
               {
                $summaryJson[$x.'-bitrate'] = 'unknown';   
               }
               
               if (isset($ffprobe['streams'][$x]['tags']['language']))
                {
                    $summaryJson[$x.'-language'] = $ffprobe['streams'][$x]['tags']['language'];
                } 
                else
               {
                   $summaryJson[$x.'-language'] = 'und';
               }
             }
        }
        
    for ($s=$audiotypecount; $s<=$streamcount; $s++) // check all streams above audio
    {
            if (($ffprobe['streams'][$s]['codec_type']=='subtitle') && ($ffprobe['streams'][$s]['codec_name']=='subrip'))
            {
                 $summaryJson[$s.'-submap']   = $ffprobe['streams'][$s]['index'];
                 if (isset($ffprobe['streams'][$s]['tags']['language']))
                {
                    $summaryJson[$s.'-language'] = $ffprobe['streams'][$s]['tags']['language'];
                    if (strlen($summaryJson[$s.'-language'])==3)
                    {
                        // 
                    }
                } 
                else
               {
                   $summaryJson[$s.'-language'] = 'und';
               }
                 
            } 
    }
      
    // send json response back
     $responseString = json_encode($summaryJson);
     $mimetype = "application/json";
     header("Cache-Control: public, must-revalidate");
     header("Pragma: no-cache");
     header("Content-Type: ".$mimetype);
     header("Created on: ".$time);                  // cache buster
     echo $responseString;
?>