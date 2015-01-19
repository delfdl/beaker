<?php 
function rationaliseAudio($inAudio) 
 {
		$comma=",";
		// work out vcodec
		$codec1 = stristr($inAudio,"Video: ");				// everything after ' '
		$codec1 = str_replace("Video: ","",$codec1);		// everything after ' '
	    $codec  = stristr($codec1," ",True); 				// everything before space
		// work out acodec
		$acodec1 = stristr($inAudio,"Audio: ");				// everything after ' '
		$acodec  = str_replace("Audio: ","",$acodec1);		// everything after ' '
		$outAudio	= $acodec;								// removed "Audio: "
     return $outAudio;
 }
 
function getAR($resolution)
{
 		$aspectDetect=explode("x",$resolution);
		$aspectX=$aspectDetect[0]/16;
		if ($aspectX<>0) 
		{
			$aspectY=$aspectDetect[1]/$aspectX;
		} else
		{
			$aspectY=1;
		}
		//echo ("aspect ratio: 16:".$aspectY."<br />");
		$aspect='16:'.intval($aspectY);
		if ($aspect=='16:12') {$aspect='4:3';}
	return $aspect;	
} 

function rationaliseVideo($inVideo) 
 {		
 		$comma=",";
		// work out vcodec
		$codec1 = stristr($inVideo,"Video: ");				// everything after ' '
		$codec1 = str_replace("Video: ","",$codec1);		// everything after ' '
	    $codec  = stristr($codec1," ",True); 				// everything before space
		// work out if SD or HD
		$whereisX 	= strrpos($inVideo,"x");					// find last x
		$resolution = substr($inVideo,$whereisX-4,9);
		$vodHeight	= stristr($resolution,"x");
		$resolution	= str_replace($comma," ",$resolution);	// strip trailing commas
		$aspect=getAr($resolution);
		// work out fps
		$whereisX 	= strrpos($inVideo,"fps");				// find last fps
		$framerate1 = substr($inVideo,$whereisX-7,10);		//
		$framerate 	= stristr($framerate1," ");				// everything after ' '
		$framerate	= str_replace($comma,"",$framerate);		// strip trailing comma	
		// work out video bitrate
		$whereisX 	= strrpos($inVideo,"kb/s");				// find last kb/s
		$bitrate1	= substr($inVideo,$whereisX-10,14);		//
		$bitrate 	= stristr($bitrate1," ");					// everything after ' ' 
		$bitrate	= str_replace($comma," ",$bitrate);		    // strip trailing comma	
		$outVideo = $codec." | ".$resolution." (".$aspect.") | ".$framerate." | ".$bitrate;		// neater video element
  	return $outVideo;
 }
 

function updateQJsonVar($jsonID,$varName,$var) 
 {
 		$newJson='';
  		$fullQueue=file_get_contents('/var/www/html/jonahv1/queue/queue.json');
		$explodeID="*";																	
		$explodeIDcount=substr_count($fullQueue,$explodeID);							//  count number of explodeIDs in $fullQueue (items in queue)
		$queueExplode=explode($explodeID,$fullQueue);									// explode queue.json by '*' into arraye
		$queue=json_decode($queueExplode[$jsonID],true);
		$queue[$varName]=$var;
		$tmpItem=json_encode($queue);
		for ($i=1; $i<=$explodeIDcount; $i++)
   		{
   			if ($i==$jsonID)
			{
				$newJson=$newJson."*".$tmpItem;				// replace json array with tmp one with updated element
			}
			else
			{
				$newJson=$newJson."*".$queueExplode[$i];	// rebuild file from individual json arrays		
			}
   		}
		if ($success=1)
		{
		copy('/var/www/html/jonahv1/queue/queue.json','/var/www/html/jonahv1/queue/queue.bak'); 		// make backup
		file_put_contents('/var/www/html/jonahv1/queue/queue.json', $newJson); 				// replace by mySql record update (add item to bottom of queue)
		}
    return $success;
 }
 
 function thumbGen($inputVOD,$i,$thumbTime)
 {  
		$fireandforget = "> /dev/null 2>/dev/null &" ; // add to exec (ffmpeg) call to make asynchronous (mmmm!)
 		$ffmpegThumb = "ffmpeg -i ".$inputVOD." -frames:v 1 -s 160x90 -ss ".$thumbTime." -f image2 '/var/www/html/jonahv1/images/thumbs/thumb-".$i.".jpg'".$fireandforget; // kick off thumbgen
		echo ("ffmpegThumb: ".$ffmpegThumb."<br />");
		if (!exec($ffmpegThumb))
			{$success=0;} 
		else 
			{$success=1;}
			echo ("success: ".$success."<br />");
    return $success;
 }
 
  function thumbGenAll($inputVOD,$i,$thumbTime,$loop)
 {  
 		$fireandforget = "> /dev/null 2>/dev/null &" ; // add to exec (ffmpeg) call to make asynchronous (mmmm!)
		$newImgDir = '/var/www/html/jonahv1/images/'.$i.'/';
		if(!file_exists($newImgDir)) 				// check if directory exists, if not make it
		{mkdir($newImgDir, 0777, true);}	
	
 		//$ffmpegThumb = "ffmpeg -i ".$inputVOD." -frames:v 1 -s 160x90 -ss ".$thumbTime." -f image2 '/var/www/html/jonahv1/images/".$i."/thumb-".$loop.".jpg'".$fireandforget; // kick off thumbgen
 		$ffmpegThumb = "ffmpeg -i ".$inputVOD." -frames:v 1 -ss ".$thumbTime." -f image2 '/var/www/html/jonahv1/images/".$i."/thumb-".$loop.".jpg'".$fireandforget; // kick off thumbgen
		$link = 'thumb-'.$loop.'.jpg';
		echo ("<tr><td colspan='2'>generating <a href='/jonahv1/images/".$i."/".$link."'>".$link."</a> at time ".$thumbTime."</td><td><div id='thumb".$loop."'>-</div></td></tr>");
		if (!exec($ffmpegThumb))
			{$success=0;} 
		else 
			{$success=1;}
    return $success;
 }
 
  function ffmpegGen($inputVOD,$preset,$presetID,$client) // file to encode e.g. 'spiderman_720.mp4', preset e.g. 'tvp', $presetID e.g. 1, client e.g. 'mtv'
 {  
		$fireandforget = "> /dev/null 2>/dev/null &" ; // add to exec (ffmpeg) call to make asynchronous (mmmm!)
	
		$presettoget = strtolower($presetpath.$preset."-preset.json");
		$fullPreset=file_get_contents($presettoget); 											// 	replace by mySql record update (add item to bottom of queue)
		$presetExplode=explode('*',$fullPreset);												// explode preset by '*'
		$tempjsonProfile = $presetExplode[1];
		$jsonProfile = 	json_decode($tempjsonProfile, true);

 		// $ffmpegCall = "ffmpeg -i ".$inputVOD." ".$part1." ".$metaJonah." ".$part2." ".$outputVOD." ".$fireandforget;
	
		if ($jsonProfile['-c:v']=='none')														// if no video, ie audio-only (usually for iOS requirement) 
			{
			$part1=' -vn ';
			}
		else 
			{
			$part1=' -c:v '.$jsonProfile['-c:v'].' -b:v '.$jsonProfile['-b:v'].' -s '.$jsonProfile['-s'].' -r '.$jsonProfile['-r'].' -g '.$jsonProfile['-g'].' '.$jsonProfile['addition1'];
			}
		
		if ($jsonProfile['-c:a']=='none')							// if no audio, ie video only (usually for analyse pass)
			{
			$part2=' -an';
			}
		else 
			{
			$part2=' -c:a '.$jsonProfile['-c:a'].' -ac '.$jsonProfile['-ac'].' -ar '.$jsonProfile['-ar'].' -b:a '.$jsonProfile['-b:a'].' '.$jsonProfile['addition2'];
			}
		//if (!exec($ffmpegThumb))
		//{$success=0;} 
		//else 
		//{$success=1;}
    return $success;
 }
 

function removeQuotes($quotedfilePath)
 {
	$nonquotedfilePath=str_replace("'","",$quotedfilePath);
	return $nonquotedfilePath; 
 }


function xml2jonah($assetxml) 		// receives filename (e.g. mtv-rihanna_where_have_you_been_720.mp4)
 {
		$tmpassetxml 	= stristr($assetxml,"."); 		// remove file suffix (everything from left of last .

		//echo ("assetxml/1: ".$assetxml."<br />");
		//echo ("tmpassetxml: ".$tmpassetxml."<br />");

		$tmpassetxml = str_replace($tmpassetxml,"",$assetxml);

		$assetxml 		= $tmpassetxml.".xml"; 		//
		//echo ("assetxml/2: ".$assetxml."<br />");
		// load pathtoxml

		//echo ("<br />checking... ".$assetxml."<br />");
		if (file_exists($assetxml))
		{
		$doc = new DOMDocument(); 
		$doc->load($assetxml);							//xml file loading here 
 
		$vodingests = $doc->getElementsByTagName("vodingest"); 
		foreach($vodingests as $vodingest) 
		{ 
  			$tmpTitle = $vodingest->getElementsByTagName("title"); 
  			$title = $tmpTitle->item(0)->nodeValue;
    		$desc = $vodingest->getElementsByTagName("description"); 
  			$description = $desc->item(0)->nodeValue;
      		$tmpSeries = $vodingest->getElementsByTagName("series"); 
  			$series = $tmpSeries->item(0)->nodeValue;
 	  		$xmlArray = array($title,$description,$series);
  		} 
	}
		else
		{
			//echo ("xml not found<br />");
			$xmlArray=array("","","");
		}
		return $xmlArray;
 } 	


function registerVideo($cat,$client,$filename) // eg. (tvp, mtv, mtv-rihanna-mandown.mp4)
 {
		$registerVid = array();
		$registerVid['epg_id'] = "";
		$registerVid['metadata'] = '{"transcode engine":"jonah"}';
		$registerVid['title'] = $filename;
		$registerVid['channel'] = $client;
		$registerVid['type'] = "vod";
		$registerVid['organisation_id'] = $cat;
		$registerVidJson=json_encode($registerVid);
		$fullQueue = file_get_contents('/var/www/html/jonahv1/admin/config.json');	// get path to API e.g. ossipon.dev.mme.smplstrm.in/
		$adminVar = json_decode($fullQueue,true);
		$vidmanapi = $adminVar['vidmanapi'];
		$vidmanapiurl = 'http://'.$vidmanapi.'videos';
		// echo ("vidmanapiurl: ".$vidmanapiurl."<br />");
		
		$ch = curl_init($vidmanapiurl);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $registerVidJson);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    	'Content-Type: application/json',                                                                                
    	'Content-Length: '.strlen($registerVidJson))                                                                       
		);                                                                                                                   
 
	$videoid = curl_exec($ch);
	return $videoid;
 } 

function registerVideoToCat($videoid, $channelID) // eg. (1234, 2)
 {
		$registerVid = array();
		$registerVid['videoId'] = $videoid;
		$registerVidJson=json_encode($registerVid);
		//$channelID = 104;			// amend to test submissions
		
		$fullQueue = file_get_contents('/var/www/html/jonahv1/admin/config.json');	// get path to API e.g. ossipon.dev.mme.smplstrm.in/
		$adminVar = json_decode($fullQueue,true);
		$vidmanapi = $adminVar['vidmanapi'];
		$vidmanapiurl = 'http://'.$vidmanapi.'categories/'.$channelID.'/videos';
		// echo ("vidmanapiurl: ".$vidmanapiurl."<br />");
		
		$ch = curl_init($vidmanapiurl);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $registerVidJson);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    	'Content-Type: application/json',                                                                                
    	'Content-Length: '.strlen($registerVidJson))                                                                       
		);                                                                                                                   
 
	$success = curl_exec($ch);
	return $success;
 } 


function updateVidMan($videoid, $key, $value) // eg. (tvp, mtv, mtv-rihanna-mandown.mp4)
 {
	$fullQueue=file_get_contents('/var/www/html/jonahv1/queue/queue.json');
	$explodeID="*";																	
	$explodeIDcount=substr_count($fullQueue,$explodeID);
	$queueExplode=explode($explodeID,$fullQueue);
	//for each $queueExplode as $queueVod
	
	foreach($queueExplode as $queueVod)
	{
	// echo ("queuevod: ".$queuevod."<br />");
	if ($queueVod) 								// if queueVod isnt empty
	{
		$queue = json_decode($queueVod,true);	
		if ($queue['epgid'] == $videoid) 
		{
			//echo ("queue['epgid'] ".$queue['epgid']." matches videoid ".$videoid." <br />");
			// use videoid to find item in queue.json, read in duration, read in timestamp, channel, client
			// load queue.json
			// explode into array
			// for each loop, check $videoid = $epgid, load info
			
			$tmpclient = $queue['client'];
			echo ("q-client: ".$tmpclient."<br />");
			$tmpcat = $queue['category'];
			echo ("q-cat: ".$tmpcat."<br />");			
			
			$orgid = getorgID($tmpcat);				// use endpoint to lookup $orgID by $cat
			$channelID = getaliasID($tmpclient,$orgid);	// use endpoint to lookup $channelID by $client
			echo ("looking up channelID by client (".$tmpclient.") and orgID (".$orgid.")<br />");
	
			$updateVM 								= array();
			$updateVM['video_id'] 		= $videoid;
			// $updateVM['epg_id']		= "";
			$updateVM['metadata'] 		= '{"transcode engine":"Jonah"}' ;;					// use description?
			$updateVM['title'] 				= $queue['filename'];
			$updateVM['channel'] 			= $channelID;
			$updateVM['duration'] 		= $queue['duration'];
			//echo ("updateVM[duration] ".$updateVM['duration']."<br />");
			$updateVM['created']			= $queue['timestamp'];
			//$updateVM['format']			= $queue['format'];
			$updateVM['format']				= "sd";
			$updateVM['seed']					= 1;					// seed=1 (ingested and transcoded, but publishing is subject to publish window)
			$updateVM['type'] 				= "vod";
			$updateVM['organisation_id']= $orgid; 				// look up organisation_id as a number?
			$updateVM['image']				= 'http://37.188.116.67/jonahv1/images/thumbs/thumb-'.$videoid.'.jpg'; 
			
			echo ("registering video ".$videoid." to channel ".$channelID." || ");
			//$success = registerVideoToCat($videoid,$channelID);
			$success = registerVideoToCat($videoid,$channelID);
			echo ("response from Dave: ".$success."<br />");
			
			$fullConfig = file_get_contents('/var/www/html/jonahv1/admin/config.json');	// get path to API e.g. ossipon.dev.mme.smplstrm.in/videos
			$adminVar = json_decode($fullConfig,true);
			$pathtoxml = $adminVar['pathtoxml'];		
		
			//$assetxml = $pathtoxml.$assetxml;
			$assetxml = $adminVar['pathtoxml'].$queue['filename'];
			// work out url -  // do we need this? or can we follow a pattern?
			// do we need ability to notify if VOD is SD or HD
		
			$updateVM['hds_url'] = 'tbc';
			$updateVM['hls_url'] = 'tbc';
		
			// check for xml, read in title, description 
			echo ("checking for xml for '".$assetxml."'<br />");		
			$xmlArray = xml2jonah($assetxml);			// checking
			if ($xmlArray[0]) 
			{	//echo ("xml found - extracting ...<br />");
				$updateVM['title']		= $xmlArray[0];
				$updateVM['metadata']	= json_encode(array("description" => $xmlArray[1]));
				$check='{}'; // check to see if metadata is populated with nothing
				if ($updateVM['metadata'] == $check)
				{$updateVM['metadata'] = '{"transcode engine":"Jonah™"}' ;}
				$updateVM['series']		= $xmlArray[2];
			}
			else 
			{
				echo ("xml not found <br />");
				$updateVM['title'] 	  = $queue['filename'];
				$updateVM['metadata'] = '{"transcode engine":"Jonah™"}';
			}
		
			$updateVMJson=json_encode($updateVM);
			echo ("<tr><td>updateVMJson </td><td>".$updateVMJson."</td><td></td></tr>");
		
			$fullConfig = file_get_contents('/var/www/html/jonahv1/admin/config.json');	// get path to API e.g. ossipon.dev.mme.smplstrm.in/videos
			$adminVar = json_decode($fullConfig,true);
			$vidmanapi = $adminVar['vidmanapi'];
			$vidmanapiurl = 'http://'.$vidmanapi."videos/".$videoid;
			// echo ("vidmanapiurl: ".$vidmanapiurl."<br />");
		
			$ch = curl_init($vidmanapiurl);                                                                      
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
			curl_setopt($ch, CURLOPT_POSTFIELDS, $updateVMJson);                                                                  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    		'Content-Type: application/json',                                                                                
    		'Content-Length: '.strlen($updateVMJson))                                                                       
			);                                                                                                                   
 
			$result = curl_exec($ch);
		
			} // end if $queueid = video_id
		} // end if ($queuevod)
	} // end foreach $queueExplode
	
	return $result;
 }


function updateFastTrackJson($epgid,$i,$url)
 {
	//echo ('received '.$epgid.','.$i.','.$url.'<br />');
	$jsonFile = '/var/www/html/jonahv1/queue/fasttrack/'.$epgid.'.json';
	if (!file_exists($jsonFile))
	{
	echo ($jsonFile." doesnt exist, creating it<br />");
	$tmpHandle = fopen($jsonFile, 'w') or die("can't create ".$jsonFile);
	fwrite($tmpHandle, "");		// creating blank file with write permissions
	fclose($tmpHandle);
	}
	$fullJson=file_get_contents($jsonFile);
	$ftJson=json_decode($fullJson,true);
	
	if (file_exists($url))
	{
		$currenttime	= date("U");
		$ftfilesize 	= intval((filesize($url)/1024));					// filesize in kilobytes
		$fttimestamp 	= $videoagefull=date("U",filemtime($url));
		$videoage		= intval((($currenttime-$videoagefull)/1));			// /1=seconds, /60=minutes
		if ($videoage>10000)
		{
			$ftfilesize = $ftfilesize.' (pre-exists)';	// if file hasnt been updated in 3 hours, assume preexists
		} 
		if ($videoage>15 && $videoage<10000)
		{
			$ftfilesize = $ftfilesize.' (complete)';	// if file hasnt been updated in 15 seconds, assume finished
		} 
		$ftJson[$i] 	= $ftfilesize;
	}
	else
	{
		$ftJson[$i] = 'pending...';
		$fttimestamp 	= 0;
	}
	$newJson		= json_encode($ftJson);
	file_put_contents($jsonFile, $newJson);
	//$fullJson
	return $fttimestamp;
 }


function getaliasID($client,$cat) 
 { 
	// $client 'mtv' ie $alias // channel alias
	// $cat 'tvp' ie $organisation // organisation
	$fullConfig = file_get_contents('/var/www/html/jonahv1/admin/config.json');	// get path to API e.g. ossipon.dev.mme.smplstrm.in/
	$adminVar = json_decode($fullConfig,true);
	$vidmanapi = $adminVar['vidmanapi'];
	
	if (!intval($cat))
	{
		echo ('organisation '.$cat.' not numeric<br />');
		// get organisation_id by name
		$orgid = getorgID($cat);
		echo ("using numeric ".$orgid."<br />");
	} else
	{
		$orgid = $cat;
		echo ("using existing ".$orgid."<br />");	
	}
	
	if (!$orgid) {$orgid='2';echo("no orgid, reverting to tvp<br />");}

	$url = 'http://'.$vidmanapi.'categories?alias='.$client.'&organisation_id='.$orgid;

	$ch = curl_init(); 
	$timeout = 5; 
	echo ("initiating ".$url."<br />");
	curl_setopt($ch,CURLOPT_URL,$url); 
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
	$response = curl_exec($ch); 
	curl_close($ch); 

	$tmp = json_decode($response,true); 
	$cList=$tmp[0];

	$aliasid = $cList['id'];
	return $aliasid;
 }


function getorgID($cat)
 { 
	// $client 'mtv' ie $alias // channel alias
	// $cat 'tvp' ie $organisation // organisation
	$fullConfig = file_get_contents('/var/www/html/jonahv1/admin/config.json');	// get path to API e.g. ossipon.dev.mme.smplstrm.in/
	$adminVar = json_decode($fullConfig,true);
	$identmanapi = $adminVar['identmanapi'];

	$url = 'http://'.$identmanapi.'organisations/?alias='.$cat;

	$ch = curl_init(); 
	echo ("initiating ".$url."<br />");
	$timeout = 5; 
	curl_setopt($ch,CURLOPT_URL,$url); 
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
	$response = curl_exec($ch); 
	curl_close($ch); 

	$tmp = json_decode($response,true); 
	$cList=$tmp[0];

	$orgid = $cList['id'];
	return $orgid; 
 }

function getorgbyDir($watchfolder)
{
	$tmpArray 		= explode("/",$watchfolder);	// explode /home/tvp/mtv into array
	//echo ("watchfolder: ".$watchfolder."<br />");
	$tmpfullCount 	= count($tmpArray);
	//echo ("tmpfullCount: ".$tmpfullCount."<br />");
	$orgCount 		= $tmpfullCount-2;				// element count, -2 for explode array character, -1 for array starting with '0'
	$orgDir 		= $tmpArray[$orgCount];
	return $orgDir;
}

function getCDN($org) 
{
	// look up cdn by vod and organisation from Lingard
	$activeCDN='ams5.ss2.tv';
	return $activeCDN;	
}

function foldersize($path) {
    $total_size = 0;
    $files = scandir($path);
    $cleanPath = rtrim($path, '/'). '/';
    foreach($files as $t) {
        if ($t<>"." && $t<>"..") {
            $currentFile = $cleanPath . $t;
            if (is_dir($currentFile)) {
                $size = foldersize($currentFile);
                $total_size += $size;
            }
            else {
                $size = filesize($currentFile);
                $total_size += $size;
            }
        }   
    }
    return $total_size;
}

?>