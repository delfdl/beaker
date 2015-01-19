<?php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     
// variables

	$cache_interval = 60; // only fetch new update if over x seconds
	$use_local      = 0;
  $time_diff      = 0;	
  $localpath      = '/var/www/html/';
  $localjson      = $localpath.'test/data/nginx.xml';					// local copy of nginx/stat
  $localbw        = $localpath.'test/data/localbw.json';			// just bandwidth high / low by hour (last 14 days by hour)// need to revise this or grab every minute?
  $localstats     = $localpath.'test/data/localstats.json';   // json store of relevant nginx stats
  $notMysql       = $localpath.'test/data/notMysql.json';     // json store of relevant nginx stats
	$exturl         = 'http://rtmp-qa1.projectapollo2.com:8080/stat';
	$run_time       = date("U");
  $primaryEnc     = '0';
	$secondaryEnc   = '0';
	$overallbw      = '0';
		
// functions

	function getCurlData($url)
	{
     $ch = curl_init($url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
     $contents = curl_exec($ch);
     curl_close($ch);
     return $contents;
	}    
  function grabNode($nodeName,$chunk)
	{     
  	$openTag = '<'.$nodeName.'>';
  	$closeTag = '</'.$nodeName.'>'; 
  	$nodeTrash = stristr($chunk,$openTag);						// grab everything after the opening tag
  	$nodeContents = stristr($nodeTrash,$closeTag,true);	// drop everything after the closing tag 
  	// mixed str_replace ( mixed $search , mixed $replace , mixed $subject [, int &$count ] )
  	$nodeClean = str_replace($openTag,'',$nodeContents);  
  return $nodeClean;
	}
	
// html start

$nodeCount = 0;

// create json array


if (file_exists($localbw)) 
	{
		//echo ('grabbing local bw json<br />');
 	  $bwString = file_get_contents($localbw);
    //echo ('bwArray: '.$bwString.'<br />');
    $bwArray = json_decode($bwString,true);
    
    $bwMax = $bwArray['bwMax'];
	  $bwMin = $bwArray['bwMin'];
	  // $bwDP  = $bwArray['bwDP'];
	  // echo ('start: local bwMin/bwMax loaded: '.$bwMin.'/'.$bwMax.'<br />');
  }
	
if (file_exists($localjson)) 
	{
		//echo ('local file found...<br />');
		$time_of_last_cache = date("U",filemtime($localjson));
  	//echo ('$localjson tlm: '.$time_of_last_cache.'<br />');
  	$time_diff = intval($run_time-$time_of_last_cache);
    //echo ('time_diff: '.$time_diff.'s<br />');
  }
  
if ((!file_exists($localjson) || ($time_diff>$cache_interval))) 
	{
		//echo ('using remote version<br />');
 		$raw_nginx = getCurlData($exturl);   
 		file_put_contents($localjson, $raw_nginx);           // create local cache for use by GUI
	}
else 
	{
		//echo ('using local cache version<br />');
	  $raw_nginx = file_get_contents($localjson); 
	}


// array explode ( string $delimiter , string $string [, int $limit ] )
$chunks = explode("<stream>", $raw_nginx);
$noofstreams = count($chunks)-1;
//echo ('streams:'.$noofstreams.'<br />');

foreach($chunks as $chunk) 
	{
		
	if (!strpos($chunk,'<application>')) 
	{
		//echo ('application not found<br />');	
	} 
		else 
	{
		// echo ('*** application found ***<br />');	
	  $overallbw = grabNode('bw_in',$chunk);	// get overall bandwidth
	  $overbw_adj = round(($overallbw/1024)/1024,2);
	  //echo ('overall bandwidth: '.$overbw_adj.' mb/s<br />');
	  
	 // echo ('subroutine: local bwMin/bwMax loaded: '.$bwMin.'/'.$bwMax.'<br />');
	  
	 	$fuckingJson   		 = array();
	  $fuckingJson['id'] = 'bandwidth';
	
	  if ($bwMin > $overbw_adj) {$bwMin = $overbw_adj;}
	  if ($bwMax < $overbw_adj) {$bwMax = $overbw_adj;}
	
	  $fuckingJson['bwMin'] = $bwMin ;
	  $fuckingJson['bwMax'] = $bwMax ;
	}  
		
	$nodename = grabNode('name',$chunk);
	// echo ('stream: '.$nodename.'<br />');	
	
	//var $fred;
	$bwin = grabNode('bw_in',$chunk);
	$bwinval = intval(intval($bwin)/1024);
	$bwinval2 = round(($bwinval/1024),2);
	// echo ('bandwidth in: '.$bwinval.' kb/s ');	
	
	$bwVideo = grabNode('bw_video',$chunk);
	$bwVideoval = intval(intval($bwVideo)/1024);
	
	$bwAudio = grabNode('bw_audio',$chunk);
	$bwAudioval = intval(intval($bwAudio)/1024);
	
	$timeSince = grabNode('time',$chunk);

	$tSecs    = intval(($timeSince/1000)); // from millisecs to secs
	$tHours   = intval(($tSecs / 3600));
	$tMins    = intval(($tSecs - ($tHours*3600))/60);
	$tSeconds = intval($tSecs - (($tHours*3600)+($tMins*60)));

		if (strlen($tHours)==1) {$tHours='0'.$tHours;}  // pad to 2 figures, ie 08
		if (strlen($tMins)==1) {$tMins='0'.$tMins;}
		if (strlen($tSeconds)==1) {$tSeconds='0'.$tSeconds;}
		$totalTime = $tHours.':'.$tMins.':'.$tSeconds;
	
	// echo (' - breakdown: '.$bwVideoval.' kb/s video | '.$bwAudioval.' kb/s audio, connected for '.$tHours.'h '.$tMins.'m '.$tSeconds.'s ');
	
	// grab video stream info here
		$vWidth   = grabNode('width',$chunk);
		$vHeight  = grabNode('height',$chunk);
		$vfps     = grabNode('frame_rate',$chunk);
		$vCodec   = grabNode('codec',$chunk);
		$vProfile = grabNode('profile',$chunk);
		$vLevel   = grabNode('level',$chunk);
		$vSummary = $vWidth.'x'.$vHeight.' | '.$vfps.'fps'.' | '.$vCodec.' '.$vProfile.' '.$vLevel;				
	
	// grab audio stream info here
	$audiochunk = explode("<audio>", $chunk);
	if (count($audiochunk)>1) 
	{
	// use stream[1]
	$aCodec   = grabNode('codec',$audiochunk[1]);
	$aProfile = grabNode('profile',$audiochunk[1]);	
	$aChannel = grabNode('channels',$audiochunk[1]);
	$aTrue    = 'undefined';
	switch ($aChannel) 
		{
       case '1':
        $aTrue = 'mono';
        break;
       case '2':
        $aTrue = 'stereo';
        break;
		}
	$aSample  = grabNode('sample_rate',$audiochunk[1]);
	$aSample  = round($aSample/1000,1);
	$aSummary = $aCodec.'-'.$aProfile.' '.$aTrue.' '.$aSample.'Khz';
	} else 
	{
	$aSummary = 'audio not found';	
	}

	// echo ($vSummary.' | '.$aSummary.' ');
	
	// explode by client
	$clients      = explode("</client>", $chunk);
  $otherClients = 0;
  $publisher    = 0;
  $primaryEnc   = 0;
  $secondaryEnc = 0;
  $publishPoints = '';
	foreach ($clients as $client)
		{
		  $address = grabNode('address',$client);
		  // echo ('address: '.$address.' - ');
			if (strpos($address,'94.56.170') !== false) 
			 {
			 	$publisher++;
			 	$otherClients++;
			 	if (strpos($address,'94.56.170.196') !== false) {$primaryEnc   = 1;}
			 	if (strpos($address,'94.56.170.197') !== false) {$secondaryEnc = 1;}			 	
        // echo ('publisher connected from '.$address.' ');
       } 
       else
       {
        $otherClients++;	
       }
		}
		$trueClients = $otherClients-$publisher-1; // correct for 'explode' function
		// echo ('clients found: '.$trueClients.' ');
		if ($primaryEnc==1) {$publishPoints=$publishPoints.' primary ';}
    if ($secondaryEnc==1) {$publishPoints=$publishPoints.' secondary ';}
	
	 echo ('<tr><td>'.$nodename.'</td><td>'.$bwinval2.' mb/s</td><td>'.$bwVideoval.' kb/s</td><td>'.$bwAudioval.' kb/s</td><td>'.$vSummary.' | '.$aSummary.'</td><td>'.$totalTime.'</td><td>'.$publishPoints.'</td><td>'.$trueClients.'</td></tr>');
	
	} 

	

    
?>