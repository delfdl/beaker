<?php 
     ini_set ("display_errors", "0");
function processCount() 
    {
        $processCount = 0; //
$commandtoUse = 'ps -f -C ffmpeg';
// ps -eo pcpu,pid,user,args | sort -r -k1 | less 

// $commandtoUse = 'ps -eo pid,%cpu,%mem,ffmpeg,www-data | sort -k 2nr | head';
exec($commandtoUse, $fred);
$fred = str_replace('  ',' ',$fred);    // sanitise double spaces

       // echo 'fred: '.$fred.'<br />';
 echo ('<table class="processTable" style="border:1px solid silver; border:1;">');
 echo ('<tr><td>&nbsp; USER&nbsp; </td><td>&nbsp; PID&nbsp; </td><td>&nbsp; CPU&nbsp; </td><td>&nbsp; STARTED&nbsp; </td><td>&nbsp; SOURCE&nbsp; </td><td>&nbsp; RESOLUTION&nbsp; </td><td>&nbsp; BITRATE&nbsp; </td><td>&nbsp; FILTER&nbsp; </td><td>&nbsp; TYPE&nbsp; </td><td>&nbsp;</td></tr>');      
       $arrayCount = count($fred);
      
        for ($loop = 1; $loop < $arrayCount; $loop++)
        {      
            $component    = '';    // video, audio, reference or thumb
            $resizeOn     = ' - ';    // is being padded (letterbox, curtains etc)
            $audioBitrate ='';
            $videoBitrate ='';
            $processInfo  = explode(' ',$fred[$loop]);
            $processUser  = $processInfo[0];
            $processPid1  = $processInfo[1];
            $processPid   = $processInfo[2];
            $processCpu   = $processInfo[3];
            $processStart = $processInfo[4];
            $processTaken = $processInfo[9];
            
            if (intval($processInfo[2])>0) // only show valid individual processes
            {
                
              //$intCommand = 'ps -eo pcpu,pid,user,args | sort -r -k1 | less';
              $intCommand = 'ps -eo pcpu,pid,args | sort -k 1 -r | head -10';
              exec($intCommand, $notfred);
              $arrayCount = count($notfred);
              for ($intloop = 1; $intloop < $arrayCount; $intloop++)
                {
                if ((strpos($notfred[$intloop],$processPid)) || (strpos($notfred[$intloop],$processPid1)))  // match pid
                    {
                        $cpuCount = explode(' ',$notfred[$intloop]);
                        $processCpu = ceil(($cpuCount[1])/16);                                              // replace cpu with pcpu
                    }
                }  
                
            $processCount++;    
            echo ('<tr>'); 
            echo ('<td>'.$processUser.'</td>');
            echo ('<td>'.$processPid.'</td>');
            echo ('<td>&nbsp;'.$processCpu.'%&nbsp;</td>');
            echo ('<td>'.$processStart.' | '.$processTaken.'</td>');
            //echo ('<td>'.$fred[$loop].'</td>');
            
            $testCount = count($processInfo);
                    for ($newloop = 0; $newloop < $testCount; $newloop++)
                    {
                       if ((strpos($processInfo[$newloop],'i',0)) && (strpos($processInfo[$newloop+1],'home',0))) 
                       {
                          $processSrc = $processInfo[$newloop+1];
                       } 
                       if ((strpos($processInfo[$newloop],'s',0)) && (strpos($processInfo[$newloop+1],'x',0))) 
                       {
                           $processRes = $processInfo[$newloop+1];
                       } 
                       if (strpos($processInfo[$newloop],'b:v',0))
                       {
                           $videoBitrate = ($processInfo[$newloop+1]/1000).'K';
                       }
                       if (strpos($processInfo[$newloop],'a:v',0))
                       {
                           $audioBitrate = ($processInfo[$newloop+1]/1000).'K';
                       }
                       if (strpos($processInfo[$newloop],'cale=',0))
                       {
                           $resizeOn = 'yes';
                       }
                    }
            
            echo ('<td>'.$processSrc.'</td><td>'.$processRes.'</td><td>'.$audioBitrate.' | '.$videoBitrate.'</td><td>'.$resizeOn.'</td>');
            
                if (strpos($fred[$loop],'video transcode',0))
                {
                $component = 'video transcode';
                }
                if (strpos($fred[$loop],'audio transcode',0))
                {
                $component = 'audio transcode';
                }
                if ((strpos($fred[$loop],'thumb transcode',0)) || (strpos($fred[$loop],'thumbnail',0)))
                {
                $component = 'thumbnail';
                } 
                if (strpos($fred[$loop],'reference transcode',0))
                {
                $component = 'QC';
                } 
                echo ('<td>'.$component.'</td>');
                echo ('<td><!--// '.$fred[$loop].' --></td>');
                                       
 
        echo ('</tr>');
            }
        } 
       // echo 'processCount: '.$processCount;
       
 if ($processCount<1) 
 {
     echo ("<tr><td colspan='10'> &nbsp; No active transcoding processes found &nbsp;</td></tr>");
 }
       
 echo ('</table>');       
        
        
    }
processCount();
//echo ('---------------------<br /><br />');


   

?>
