<?php
     
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
	$channelbw      = array("","","","","","","","","","");	
	$descLeg        = array("","White Tigers","African Lions","African Wild Dogs","standby","Meerkats","African Mixed pole","African Mixed hut","");
	
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

//echo ('<html><head>');
//echo ('<meta http-equiv="refresh" content="60" />');
//echo ('</head><body>');
//echo ('local time: '.$run_time.'<br />');
//echo ('checking local time...<br />');
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
	  //echo ('start: local bwMin/bwMax loaded: '.$bwMin.'/'.$bwMax.'<br />');
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

//echo ('<textarea cols=160 rows=40>');
//echo $raw_nginx;
//echo ('</textarea><br />');

// array explode ( string $delimiter , string $string [, int $limit ] )
$chunks = explode("<stream>", $raw_nginx);
$noofstreams = count($chunks)-1;
//echo ('streams:'.$noofstreams.'<br />');

foreach($chunks as $chunk) 
	{
		//echo ("<textarea cols=160 rows=5>");
		//echo ($chunk);
		//echo ("</textarea><br />");
		//$chunkCount++;
		
	if (!strpos($chunk,'<application>')) 
	{
		//echo ('application not found<br />');	
	} 
		else 
	{
		//echo ('*** application found ***<br />');	
	  $overallbw = grabNode('bw_in',$chunk);	// get overall bandwidth
	  $overbw_adj = round(($overallbw/1024)/1024,2);
	  //echo ('overall bandwidth: '.$overbw_adj.' mb/s<br />');
	  //echo ('subroutine: local bwMin/bwMax loaded: '.$bwMin.'/'.$bwMax.'<br />');
	  
	 	$fuckingJson   		 = array();
	  $fuckingJson['id'] = 'bandwidth';
	
	  if ($bwMin > $overbw_adj) {$bwMin = $overbw_adj;}
	  if ($bwMax < $overbw_adj) {$bwMax = $overbw_adj;}
	
	  $fuckingJson['bwMin'] = $bwMin ;
	  $fuckingJson['bwMax'] = $bwMax ;
	}  
		
	$nodename = grabNode('name',$chunk);
	//echo ('stream: '.$nodename.'<br />');	
	
	//var $fred;
	$bwin = grabNode('bw_in',$chunk);
	$bwinval = intval(intval($bwin)/1024);
	//echo ('bandwidth in: '.$bwinval.' kb/s ');	
	
	$bwVideo = grabNode('bw_video',$chunk);
	$bwVideoval = intval(intval($bwVideo)/1024);
	
	$bwAudio = grabNode('bw_audio',$chunk);
	$bwAudioval = intval(intval($bwAudio)/1024);
	
	$timeSince = grabNode('time',$chunk);

	$tSecs    = intval(($timeSince/1000)); // from millisecs to secs
	$tHours   = intval(($tSecs / 3600));
	$tMins    = intval(($tSecs - ($tHours*3600))/60);
	$tSeconds = intval($tSecs - (($tHours*3600)+($tMins*60)));

		if (strlen($tHours)  ==1) {$tHours='0'.$tHours;}  // pad to 2 figures, ie 08
		if (strlen($tMins)   ==1) {$tMins='0'.$tMins;}
		if (strlen($tSeconds)==1) {$tSeconds='0'.$tSeconds;}
	
	//echo (' - breakdown: '.$bwVideoval.' kb/s video | '.$bwAudioval.' kb/s audio, connected for '.$tHours.'h '.$tMins.'m '.$tSeconds.'s ');
	
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

	//echo ($vSummary.' | '.$aSummary.' ');
	
	// explode by client
	$clients      = explode("</client>", $chunk);
  $otherClients = 0;
  $publisher    = 0;
  
	foreach ($clients as $client)
		{
		  $address = grabNode('address',$client);
		  // echo ('address: '.$address.' - ');
			if (strpos($address,'94.56.170') !== false) 
			 {
			 	$publisher++;
			 	$otherClients++;
			 	if (strpos($address,'94.56.170.196') !== false) {$primaryEnc   = '1';}
			 	if (strpos($address,'94.56.170.197') !== false) {$secondaryEnc = '1';}			 	
        // echo ('publisher connected from '.$address.' ');
       } 
       else
       {
        $otherClients++;	
       }
		}
		$trueClients = $otherClients-$publisher-1; // correct for 'explode' function
		//echo ('clients found: '.$trueClients.' ');
	} 

// write out local json file
// $jsonValue('publishers') = $publisher;
// $jsonValue('clientcount') = $otherClients;
// $jsonString = json_encode($jsonValue);

// create json array
// write out local json file

//	$bwjsonValue['bwTitle'] = 'why isnt this working';


	//$newDP = '|'.$jsonD.':'.$jsonH.':'.$overbw_adj;
	//$fuckingJson['bwDP'] = $bwDP;
	//if ($time_diff>$cache_interval) 
	//{
	//$fuckingJson['bwDP'] = $bwdataPoints.$newDP; // only append new data point if not using locally cached xml
  //}
 
 // save Min/Max - localbw
	$bwjsonString = json_encode($fuckingJson);
	//echo ('Saving:'.$bwjsonString);
	file_put_contents($localbw, $bwjsonString); // saving local bandwidth
	
	// save Min/Max by hour/day - 
  $localstatsJson = array();
	$jsonD = date('d'); // record day of the month ie 14 (14th)
	//echo ('jsonD: '.$jsonD.'<br />');
	$jsonH = date('H'); // record hour of the day ie 23 (11pm-11:59)
	//echo ('jsonH: '.$jsonH.'<br />');
	
	if (file_exists($localstats)) 
	{
 	  $statString = file_get_contents($localstats);
    // echo ('statString (from file contents): '.$statString.'<br />');
    $statDays = explode('*',$statString); // asterix delimited json chunks :)
    $refCount = count($statDays);
    // echo (''.$refCount-1.' bits found <br />');
    $latestDays = $statDays[$refCount-1]; // load last stat
    $localstatsJson = json_decode($latestDays,true);
    //echo ("found local stat file - ".$localstatsJson['h'].",".$localstatsJson['d'].",".$localstatsJson['min'].",".$localstatsJson['max']."<br />");
    
  		if (($localstatsJson['h']==$jsonH))
  		{ 
  		  //echo ('hour matched<br />');
			  if ($localstatsJson['max'] < $overbw_adj) {$localstatsJson['max'] = $overbw_adj;}
			  if (($localstatsJson['min'] > $overbw_adj) && ($overbw_adj>0)) {$localstatsJson['min'] = $overbw_adj;}
			  $updatedStats = json_encode($localstatsJson);
			  $statLimit = $refCount-1;
			}
  		else 
  		{
  			//echo ('new hour because old hour = '.$localstatsJson['h'].'and new hour = '.$jsonH.'<br />');
  			$localstatsJson['h']   = $jsonH;
  			$localstatsJson['d']   = $jsonD;
  			$localstatsJson['min'] = $overbw_adj;
  			$localstatsJson['max'] = $overbw_adj;
  			$updatedStats = json_encode($localstatsJson);
  			$statLimit = $refCount;	
  		}
  		
  		// $statLimit;
  		$newstatString=''; 		
      for ($i = 1; $i < $statLimit; $i++) 
      {
       $newstatString = $newstatString.'*'.$statDays[$i];
      }
      
      $newstatString = $newstatString.'*'.$updatedStats;
      
    // echo ('about to write: '.$newstatString);	
	// echo ('Saving:'.$statString);
	file_put_contents($localstats, $newstatString); // saving local stats by hour
  }
  
//echo ('Min: '.$localstatsJson['min'].'<br />');
//echo ('Max: '.$localstatsJson['max'].'<br />');
    
?>