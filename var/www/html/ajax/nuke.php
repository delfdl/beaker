<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<?php
     ini_set ("display_errors", "1");
function processCount() 
    {
        $processCount = 0; //
$commandtoUse = 'ps -f -C ffmpeg';
exec($commandtoUse, $fred);
$fred = str_replace('  ',' ',$fred);    // sanitise double spaces
     
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
              $notfred = exec($intCommand, $notfred);
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

            // nuke pid here
            $nukePid = 'sudo -9 kill '.$processPid;
            echo ('nuking PID: '.$nukePid.'<br />');
            exec($nukePid,$indexFinger);

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

            }
        } 

       
 		if ($processCount<1) 
 		{
     echo ("<br />Status OK (nuke.php) &nbsp;");
 		}
        
 }
    
  $localpath      = '/var/www/html/';
  $localnuke      = $localpath.'test/data/nuke.json';					// local copy of nginx/stat 
  $nuke = array(); 
  
  if (file_exists($localnuke)) 
  {
  $nukeopt = file_get_contents($localnuke);
  $nuke    = json_decode($nukeopt,true);
  
  echo("<div id='monkey' class='formrow'><form id='nukesave' method='POST' action='#'>");  
  if ($nuke['trigger'] == '1')
  		{
  			echo ("Nuke option enabled &nbsp;<input class='floatL mr10' id='check6' type='checkbox' checked name='nuke' onClick=\"document.forms['nukesave'].submit();\"/ >");
		
  			processCount();
  		}
  		else 
  		{
  		echo ("Nuke option disabled &nbsp; <input class='floatL mr10' type='checkbox' id='check7' name='nuke' onClick=\"document.forms['nukesave'].submit();\" />");	
  		} 
  echo("</form></div>");   		
   		
  			
  }  

 