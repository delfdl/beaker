<?php 
$queueItem 	= $_GET["item"]; 
$cat 		= $_GET["cat"];
echo ("Fast-tracking queue item ".$queueItem." and transcoding with ".$cat."-preset.json<br />");
echo ("Note: fast tracking forces single pass encodes<br />");
 
//	reference exec(ffmpeg -i $input $profile $pass-specific $output $fireandforget)
	 include ('/var/www/html/jonahv1/inc/jonah-lib.php');

$adminVar=file_get_contents('/var/www/html/jonahv1/admin/config.json'); 	//read admin variable from local json file
$config = json_decode($adminVar, true);								// json decode array

$pathtoqueue	= $config['pathtoqueue'];		// where queued files sit
$pathtoxml		= $config['pathtoxml'];			// where xml (category) sits (to catalog after process)
$logpath 		= $config['logpath'];			// where to log 
$parallel		= $config['parallel'];			// how many concurrent transcodes
$passes 		= $config['passes'];			// how many passes for each transcode
$outputpath		= $config['outputpath'];		// output path for FMS vod directory 
$presetpath		= $config['presetpath'];		// where to pick up encoding profiles (presets)
$thumbpath		= $config['thumbpath'];		// where to pick up encoding profiles (presets
$manifestpath	= $config['manifestpath'];		// where to pick up encoding profiles (presets)

echo ("thumbpath: ".$thumbpath."<br />");
echo ("manifestpath: ".$manifestpath."<br />");
echo ("outputpath: ".$outputpath."<br />");

$cdnServer = 'ams5-dev.smplstrm.in';

$fireandforget = "> /dev/null 2>/dev/null &" ; // add to exec (ffmpeg) call to make asynchronous (mmmm!)
$i=0;

	$fullQueue=file_get_contents('/var/www/html/jonahv1/queue/queue.json'); 				// 	replace by mySql record update (add item to bottom of queue)
	//echo("<b>fullqueue: </b>".$fullQueue."<br />");
	$explodeID="*";																	// 	used to separate json-encoded arrays
	// $explodeIDcount=substr_count($fullQueue,$explodeID);							//  count number of explodeIDs in $fullQueue
	
	$queueExplode=explode($explodeID,$fullQueue);									// explode queue.json by '*' into array
	//for each $queueExplode as $queueVod
	$fastTrackItem = $queueExplode[$queueItem];
	//echo ("fastTrackItem: ".$fastTrackItem."<br />");
	$queue = json_decode($fastTrackItem, true);	
		
		$filename 		= $queue['filename'];	
		$tempinfo		= pathinfo($filename);												
    	$videofilename	= basename($filename,'.'.$tempinfo['extension']);			// find filename without path and extension (e.g. 'Shakira Liar')		// strip out
		$videofilename  = str_replace(" ","_",$videofilename); 						// for early compatibility, converts spaces to underscores
		$transcodeState = $queue['transcodeState'];
		$queueid 		= $queue['queue-id'];
		$cat 			= strtolower($queue['category']);
		$client 		= strtolower($queue['client']);
		$duration	 	= $queue['duration'];
		$epgid			= $queue['epgid'];
		
		$inputVOD = "'".$pathtoqueue.$filename."'";
		
		// load preset for $client
		$presettoget 	= 	strtolower($presetpath.$cat."-preset.json");			// load preset
		//echo ("loading preset: ".$presettoget."<br />");
		$fullPreset		=	file_get_contents($presettoget); 						// 	replace by mySql record update (add item to bottom of queue)
		$presetExplode	=	explode('*',$fullPreset);								// explode preset by '*'

		$explodePresetcount = substr_count("*",$presetExplode);						//  count number of profiles in category preset
		$howmanyStates 		= ($explodePresetcount*$passes*2);	
		
		$tmpDur = explode(":",$duration);
		$tmp1   = $tmpDur[0] * 360;
		$tmp2	= $tmpDur[1] * 60;
		$secDuration = $tmp1 + $tmp2 + $tmpDur[2]; 
		//echo ("secDuration: ".intval($secDuration)."<br />");
		//echo intval($secDuration)/6;
		$secInterval = intval($secDuration)/6;										// generate 5 thumbnails at equal intervals, except credits
		
		for ($progLoop=1; $progLoop<=5; $progLoop++)
   		{
				$newDuration = $secInterval*$progLoop;								// 1/6 of the way in (up to 5/6 of the way in)
				//echo ("newDuration: ".$newDuration."<br />");
   				$t1 = (intval($newDuration / 360));
				$t2 = (intval($newDuration / 60));
				$t3 = intval(fmod($newDuration,60));
				$thumbTime = "'".$t1.":".$t2.":".$t3."'";
				// echo ("tmpTime: ".$tmpTime."<br />");
				// $thumbTime= '0:0:48';	// change to 48
				$success = thumbGenAll($inputVOD,$epgid,$thumbTime,$progLoop);
   		}
		//echo ("thumbs created<br />");	

		// generate thumbnails
		
		foreach ($presetExplode as $profile)
		{
			$i++;
			$varTest = $presetExplode[$i];
					
			if ($varTest)
			{
				// echo ("verifying profile($i): ".$varTest."<br />");
				$jsonProfile = 	json_decode($varTest, true);
		
			// put together ffmpeg parameters from preset profile
			// create video
			if ($jsonProfile['-c:v']=='none')										// if no video, ie audio-only (usually for iOS requirement) 
				{
				$videoPart1=' -vn ';
				}
			else 
				{
				$videoPart1=' -c:v '.$jsonProfile['-c:v'].' -b:v '.$jsonProfile['-b:v'].' -s '.$jsonProfile['-s'].' -r '.$jsonProfile['-r'].' -g '.$jsonProfile['-g'].' '.$jsonProfile['addition1'];
				}
			// create audio
			if ($jsonProfile['-c:a']=='none')							// if no audio, ie video only (usually for analyse pass)
				{
				$audioPart2=' -an ';
				}
			else 
				{
				$audioPart2=' -c:a '.$jsonProfile['-c:a'].' -ac '.$jsonProfile['-ac'].' -ar '.$jsonProfile['-ar'].' -b:a '.$jsonProfile['-b:a'].' '.$jsonProfile['addition2'];
				}
		
				// get bitrate ie $passBitrate
			$passBitrate = $jsonProfile['-b:v'];													// read bitrate from preset? use '280' to test
			$suffix = $jsonProfile['container'];													// e.g. mp4
			// $metaJonah = " -metadata title='".$filename." (".$epgid.")' -metadata copyright='".$client."' ";
			$metaJonah = '';
			$videofileFull	= $passBitrate."/".$videofilename;											// add bitrate suffix ie 'shakira_liar-280.mp4' or '/280/shakira_liar.mp4'
			$inputVOD		= "'".$pathtoqueue.$filename."'";
			$inputVOD  		= str_replace(" ","_",$inputVOD);											// converts spaces to underscores (this problem only exists for legacy test files)
			$outputDir = strtolower($outputpath.$cat."/".$client."/".$passBitrate."/");
			$outputVOD = strtolower("'".$outputpath.$cat."/".$client."/".$videofileFull.".".$suffix."'");	// compile full output path, converting to lowercase
			$outputVOD = str_replace(" ","_",$outputVOD);													// strip spaces from output paths
			$ffmpegCall = "ffmpeg -i ".$inputVOD." ".$videoPart1." ".$metaJonah." ".$audioPart2." ".$outputVOD." ".$fireandforget;
			
			if(!file_exists($outputDir)) 				// check if out directory ie /path/280/ exists, if not make it
				{
				echo ($outputDir." doesnt exist, creating it<br />");
				mkdir($outputDir, 0777, true);
				}

			exec ($ffmpegCall);							// create output file
			echo ("creating ".$outputVOD." <br />");
			
			$manifestHDS 	 = strtolower($manifestpath.$cat."/".$client."/".$videofilename.".f4m");
			$manifestHLS 	 = strtolower($manifestpath.$cat."/".$client."/".$videofilename.".m3u8");
			$manifestHDSpath = strtolower($manifestpath.$cat."/".$client."/");
			
			if(!file_exists($manifestHDSpath)) 				// check if path to manifest exists, if not make it
				{
				echo ($manifestHDSpath." doesnt exist, creating it<br />");
				mkdir($manifestHDSpath, 0777, true);
				}
			
			if(!file_exists($manifestHDS)) 				// check if manifest file exists, if not create it and write header
				{
				echo ($manifestHDS." doesnt exist, creating it<br />");
				$HDSheader = "<manifest xlmns=\"http://ns.adobe.com/f4m/2.0\">\n<baseURL>".$cdnServer."/hds-vod/".$cat."/".$client."/</baseURL>\n";
				$tmpHandle = fopen($manifestHDS, 'w') or die("can't create ".$manifestHDS);
				fwrite($tmpHandle, $HDSheader);
				fclose($tmpHandle);
				}
			
			if(!file_exists($manifestHLS)) 				// check if manifest file exists, if not create it and write header
				{
				echo ($manifestHLS." doesnt exist, creating it<br />");
				$HLSheader = "#EXTM3U\n#EXT-X-FAXS-CM:URI=\"/hls-vod/".$cat."/".$client."/".$videofilename.".mp4.drmmeta\"\n";
				$tmpHandle = fopen($manifestHLS, 'w') or die("can't create ".$manifestHLS);
				fwrite($tmpHandle, $HLSheader);
				fclose($tmpHandle);
				}
			
			// write HDS entry
			$decBitrate = (intval($jsonProfile['-b:v']))+(intval($jsonProfile['-b:a']));
			$HDSappend = "\t<media href=\"".$passBitrate."/".$videofilename.".f4m\" bitrate=\"".$decBitrate."\"/>\n";
			$tmpHandle = fopen($manifestHDS, 'a');
			fwrite($tmpHandle, $HDSappend);
			fclose($tmpHandle);

			//echo ("manifestHLS: ".$manifestHLS."<br />");
			
			// write HLS entry
			//$decBitrate = (intval($jsonProfile['-b:v']))+(intval($jsonProfile['-b:a']));
			$HLSappend1 = "\t#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=".($decBitrate*1000)."\n";
			$HLSappend2 = "/hls-vod/".$cat."/".$client."/".$videofilename.".mp4.m3u8\n";
			$HLSappend = $HLSappend1.$HLSappend2;
			$tmpHandle = fopen($manifestHLS, 'a');
			fwrite($tmpHandle, $HLSappend);
			fclose($tmpHandle);

			//echo ("manifestHLS: ".$manifestHLS."<br />");
			// create hds manifest file, write, close
			// create hls manifest file, write close
			
			} // end if varTest
		} // end for each presetExplode
		
			$HDSappend = "</manifest>\n\n#autogenerated by Jonah";
			$tmpHandle = fopen($manifestHDS, 'a');
			fwrite($tmpHandle, $HDSappend);
			fclose($tmpHandle);
			
			$HLSappend = "\n\n#autogenerated by Jonah";
			$tmpHandle = fopen($manifestHLS, 'a');
			fwrite($tmpHandle, $HLSappend);
			fclose($tmpHandle);
			
			// send notification that <$epgid><$manifestHDS> is ready for playback
			echo ('VOD item '.$epid.' is ready for playback in Flash, using '.$manifestHDS.' manifest<br />');
			echo ('VOD item '.$epid.' is ready for playback in iOS, using '.$manifestHLS.' manifest<br />');

			echo ("Updating queueItem ".$queueItem." to transcodeState '10'");
			$update1 = updateQJsonVar($queueItem,'transcodeState',10);	
?>