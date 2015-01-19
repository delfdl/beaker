<!-- queue view -->

<table id="table-3" class='jonahtable'>
<tr class='tabhead-transcode nodrop nodrag'><td>&nbsp; <!-- drag icon --></td><td> Queue </td><td> XML </td><td> Cat </td><td> Client </td><td> File </td><td> Size </td><td> Duration </td><td> Uploaded </td><td>&nbsp;</td><td> Source </td><td> Video </td><td> Audio </td><td> Status </td><td> Preview </td><td> Estimated<br />completion </td><td>&nbsp; <!-- encode state icon --></td></tr>

<!-- sample row -->

<tr class='#transcoding nodrop nodrag'><td>&nbsp;</td><td> 0</td><td>pending</td><td>TVP</td><td> MTV-ASIA </td><td>playboy-catfight_dallas.mpg</td><td>300 Mb</td><td>01:20:00</td><td>July 23, 12:01</td><td>HD</td><td>1280x720</td><td>h264, 480x288, 997 kb/s, 24.99 fps</td><td>aac, 48 Khz, mono, s16, 63 kb/s</td><td>transcoding - 80%<br /><img src='/jonahv1/images/upload.png' /></td><td> 8/12 </td><td><img src='/jonahv1/images/transcode-live.png' /></td></tr>

<?php 
	$explodeID="*";																	// 	used to separate json-encoded arrays
	$progressBar=0;
	$i=0;																
	$fullQueue=file_get_contents('/var/www/html/jonahv1/queue/queue.json'); 				// 	replace by mySql record update (add item to bottom of queue)
	//echo ("<span class='troubleshoot'>fullQueue: </span> ".$fullQueue."<br />");
	$queueExplode=explode($explodeID,$fullQueue);
	//for each $queueExplode as $queueVod
	
	foreach($queueExplode as $queueVod)												//	
	{
	$i++;
	if ($queueVod) 
		{
		// echo ("<span class='troubleshoot'>queueVod: </span>".$queueVod."<br />");
		// $queueVod=file_get_contents('/var/www/html/jonahv1/queue/queue.json'); 			//	read vod item variable from local json file
	 	$queue = json_decode($queueVod, true);										// 	json decode array
	
		// work out if SD or HD
		$vodHeight=0;
		$comma=",";
		$detectHD = $queue['video'];
		$whereisX = strrpos($detectHD,"x");											// find last x
		$resolution = substr($detectHD,$whereisX-4,9);
		$resolution = str_replace($comma,"",$resolution);							// strip x from height	
		$vodHeight = stristr($resolution,"x");
		$vodHeight = str_replace("x","",$vodHeight);								// strip x from height	
		//echo ("vodHeight: ".$vodHeight."<br />");
		//echo ("resolution: ".$resolution."<br />");
		
		if ($vodHeight>576) 
			{
			$sdorhd="HD";
			$queue['source']='HD';
			} 
		else 
			{
			$sdorhd="SD";
			$queue['source']='SD';
			}
			
		$aspect=getAR($resolution);
			
		 if (!$queue['epgid'])
		 {
			$queue['epgid']='pending';
		 }
		
		$explodePreset 		= "*";																// 	used to separate json-encoded arrays
		$cat							= strtolower($queue['category']);
		$checkProfile			= file_get_contents('/var/www/html/jonahv1/profiles/'.$cat.'-preset.json');// load category preset
		$explodePresetcount = substr_count($checkProfile,$explodePreset);						//  count number of profiles in category preset
		$fullAdmin				= file_get_contents('/var/www/html/jonahv1/admin/config.json'); 			// 	replace by mySql record update (add item to bottom of queue)
		$jsonAdmin 				= json_decode($fullAdmin, true);
		$passes						= $jsonAdmin['passes'];
		$parallel					= $jsonAdmin['parallel'];
		$howmanyStates 		= ($explodePresetcount*$passes*2);									//  for each profile, x passes and 2 states (started/completed);
		
		$queueNumber = intval($queue['queue-id']);												// where in the queue is item?		
		
		// check if ffmpeg is processing item 
		// work out percentage done
		
		if ($queueNumber<=$parallel)
		{
			$queueClass='encodeStyle';
		}
		else
		{
			$queueClass='queueStyle';
		}
		
?>
<tr class='<?php  echo ($queueClass); ?>' id="<?php  echo $queue['queue-id']; ?>"><td>&nbsp;</td><td><?php  echo $queue['queue-id']; ?></td><td>
<?php  $xmlFile = $pathtoxml.$queue['filename'];
$xmlArray = xml2jonah($xmlFile);
if ($xmlArray[0] <> "") 
{echo ("<img src='/jonahv1/images/xmly.png' />");} else {echo ("<img src='/jonahv1/images/xmln.png' />");}
?>
</td><td><?php  echo $queue['category']; ?></td><td><?php  echo $queue['client']; ?></td><td><?php  echo $queue['filename']; ?><br /><br /><span class='body'>(ID: <?php  echo($queue['epgid']); ?>)</span></td><td><?php  echo $queue['filesize']; ?> Mb</td><td><?php  echo $queue['duration']; ?></td><td><?php  echo $queue['timestamp']; ?></td><td><?php  echo($sdorhd); ?></td><td><?php  echo ($resolution."<br />(".$aspect.")"); ?></td><td><?php  // echo ($queue['video']); ?><br /><a href='/jonahv1/inc/analyse-modal.php?analysis=<?php  echo $queue['filename']; ?>' rel="shadowbox[Mixed];width=666;height=888" />details</a></td><td><?php  //echo ($queue['audio']); ?></td><td>
<?php  	// work out progress indicator based on encodeStatus
	if ($queue['transcodeState']>0) 
	{
		// work out $progressBar
		$startloop=$queue['transcodeState'];
		$percentComplete = intval(($startloop/$howmanyStates*100));
	
		for ($progLoop=1; $progLoop<=$startloop; $progLoop++)
   		{
   			echo ("<img src='/jonahv1/images/state-on.png' />");			// show number of coloured blocks
   		}
		for ($progLoop=$startloop+1; $progLoop<=10; $progLoop++)
   		{
   			echo ("<img src='/jonahv1/images/state-off.png' />");			// show number of blank blocks
   		}
		echo ("<br />transcoding - ".$percentComplete."%  (".$queue['transcodeState']."/".$howmanyStates.")");
	}
	else 
	{
		for ($progLoop=1; $progLoop<=10; $progLoop++)
   		{
   			echo ("<img src='/jonahv1/images/state-off.png' />");			// show all blank blocks if progress hasnt started
   		}
		echo ("<br />queued");	
	}
?>
</td>
<td><?php  //  
if ($queueNumber>$parallel OR $queue['transcodeState']<1)
	{ 	
	$imgThumb = '/jonahv1/images/thumb.jpg';						// generic image (non-thumb)
	} 
else 
	{
	$imgThumb = '/jonahv1/images/thumbs/thumb-'.$queue['epgid'].'.jpg';		// show queuenumber-specific thumb (not video specific thumb)
	$fullImgPath = ('/var/www/html'.$imgThumb);
	if (!file_exists($fullImgPath)) 
		{
		echo ("<img src='/jonahv1/images/thumbs/thumb-pending.jpg' />");
		} 
	else 
		{
		echo ("<img src='".$imgThumb."' />");
		}
	}
?>
</td>
<td>pending
<?php 
	echo ("<br /><a href='/jonahv1/admin/fasttrack.php?item=".$queueNumber."&cat=".$cat."'>fasttrack</a>");
?>
</td>
<td>
<?php  
	if ($queue['transcodeState']>0 AND $queueNumber<=$parallel) 
		{ 
		echo ("<img src='/jonahv1/images/transcode-live.png' />");	// show green 'encoding' icon
		}
	else 
		{
		echo ("<img src='/jonahv1/images/transcode-q.png' />");		// show grey 'paused' icon
		}

?>

</td></tr>
<?php 
		}	
		else
		{}		// if queueVOD=''
	}			// end foreach $queue

?>
</table>