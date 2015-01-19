<?php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
  
  $localpath = '/var/www/html/';
  $moxa      = '94.56.170.194'; // moxa
  $enc1      = '94.56.170.196/hera/netconfig/'; // primary encoder
  $enc2      = '94.56.170.197/hera/netconfig/'; // secondary encoder
  $nginx     = 'rtmp-qa1.projectapollo2.com:8080/stat';	// nginx rtmp module
  $localTime = date('d/m/y-H:i');
  $localE    = date('dmy');

  $localDump = $localpath.'test/data/errors/localerrors-'.$localE.'.json';
  $lastError = $localpath.'test/data/lasterror.json';
  $errorDump = '';
 
  $beakerAdvice  = '';
  $localBeaker   = $localpath.'test/data/localbeaker.json';
  $streamCheck   = file_get_contents($localBeaker); // grabbing local bandwidth
  $streamFailure = '';
	$channelString = json_decode($streamCheck,true);
	$statusAlert   = 0;
	$overallState  = 0;
	$problemArea   = array();
	$currentTime   = (date('U')/60); // number of minutes since Epoch (1970)

if (file_exists($localDump))
{$errorDump   = file_get_contents($localDump);} // grabbing hourly errors}

	foreach ($channelString as $channelKey => $channelCheck)
	{
		if ($channelCheck!=0) 
		{
			$overallState = 1; // at least 1 stream is up
		} 
		else 
		{
			$statusAlert = 1; // at least 1 stream is down
			$streamFailure .= $channelKey.' '; // record channel that is showing '0'
		}
	}
	

function getHTTPstatus($url) 
{
  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

  /* Get the HTML or whatever is linked in $url. */
  $response = curl_exec($handle);

  /* Check for 404 (file not found). */
  $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
  curl_close($handle);
  return($httpCode);
}
 
$moxaVal  = getHTTPStatus($moxa);
$enc1Val  = getHTTPStatus($enc1);
$enc2Val  = getHTTPStatus($enc2);
$nginxVal = getHTTPStatus($nginx); 
 
?>
            <ul class="messagesOne">
                <li class="by_user">
                    <a href="#" title="">
                    	                        <?php 
                        if ($statusAlert==0) 
                        {
                          echo ('<img src="/images/beaker.jpg" alt="" />')	;
                        } 
                        else
                        {
                         	echo ('<img src="/images/beaker2.jpg" alt="" />')	;
                        }
                        
                        ?>
                        </a>
                    <div class="messageArea">
                        <span class="aro"></span>
                        <div class="infoRow">
                            <span class="name"><strong>Beaker 1.0</strong>&trade; says:</span>
                            <span class="time">1 minute ago</span>
                        </div>
                        <!-- load message here -->
<?php 
                        if ($statusAlert==0) 
                        {
                        	// no streams down
                         if ($moxaVal=="200")
                          {	
                          	if (file_exists($lastError)) {$lastTime = file_get_contents($lastError);} else {$lastTime = 'unknown';} 
                          	$actualLastTime = explode('|',$lastTime);
                          	$currentNow = (date('U')/60);
                          	$howLong = $actualLastTime[1];
                          	if (intval($howLong+1)==intval($currentTime))  // if the last issue was a minute ago
                          	{
                          	 $errorDump .= $localTime.' | All Clear'."\n";
                         		 file_put_contents($localDump,$errorDump);	
                          	}
                            echo ('"There don\'t seem to be any current issues" &nbsp; &nbsp; (last error was <a href="/messages.php">'.$actualLastTime[0].'</a>)<br />');
                            
                          }
                          else 
                          {
                          	echo ('"There may be a problem with camera control - moxa UI isnt responding"<br />');
                          }
                        } 
                        else
                        {
                            echo ('"There might be a problem... dont panic."<br />');
                            if ($overallState==0) 
                            {
                            	// all streams down or unable to get status from nginx xml
                            	$beakerAdvice .= 'Unable to validate any stream status. ';
                            	if ($nginxVal!="200") {$beakerAdvice .= 'Unable to get response from NGINX proxy. ';}
                         		  if (($enc1Val!="200") && ($enc2Val=="200")) {$beakerAdvice .= 'The primary encoder isnt responding - try switching to backup encoder. ';}
                         		  if (($enc1Val!="200") && ($enc2Val!="200") && ($moxaVal!="200")) {$beakerAdvice .= 'There may be an issue with the Etisalat connectivity. ';}
                         		  echo ($beakerAdvice.'<br />');
                         		  // save
                         		  $errorDump .= $localTime.' | '.$beakerAdvice.' | '.$streamFailure.' | '.$streamCheck."\n";
                         		  file_put_contents($localDump,$errorDump);
                         		  file_put_contents($lastError,$localTime);
                            } else
                            {
                              // at least 1 stream down	
                            	echo ("At least 1 stream seems to be down<br />");
                            	if (($channelString['aa-port6']=="0") && ($channelString['aa-port7']=="0") && ($channelString['aa-port6-monitor']=="0") && ($channelString['aa-port7-monitor']=="0") && ($moxaVal!="200")) {$beakerAdvice .= 'The power may be out to the outdoor rack (BETA). ';}
                            	$beakerAdvice .= ' | '.$streamFailure.' | ';
                            	echo ($beakerAdvice.'You may need to restart some streams via the encoder HMS <br />If the streams have been down for more than 90 seconds, you will also need to restart the transcode instance via the console after the stream has reconnected.<br />');
                            	// save
                            	$errorDump .= $localTime.' | '.$beakerAdvice.' | '.$streamFailure.' | '.$streamCheck."\n";
                         		  file_put_contents($localDump,$errorDump);
                         		  file_put_contents($lastError,$localTime.'|'.$currentTime);
                            }
                        }
?>
                    </div>
                </li>
            </ul>
 <div class="body" align="center">           
<?php
echo ("<span style='border:1px solid silver;' title='moxa camera control' class='buttonS bDefault tipN'> MOXA  ".$moxaVal."  </span>&nbsp;");                   
echo ("<span style='border:1px solid silver;' title='primary encoder'     class='buttonS bDefault tipN'> ENC1  ".$enc1Val."  </span>&nbsp;");  
echo ("<span style='border:1px solid silver;' title='secondary encoder'   class='buttonS bDefault tipN'> ENC2  ".$enc2Val."  </span>&nbsp;");  
echo ("<span style='border:1px solid silver;' title='nginx proxy'         class='buttonS bDefault tipN'> NGINX ".$nginxVal."  </span>&nbsp;");                      
?> 
</div>          
      
            
<?php
?>