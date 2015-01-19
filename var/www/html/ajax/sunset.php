<?php

    ini_set ("display_errors", "1");
    error_reporting(E_ALL);
    
  $sslocation     = '24.177/55.74'; // Al Ain Zoo latitude and longitude, according to googlemaps
  $scacheInterval = 86400; // grab daily
  $localpath      = '/var/www/html/';
  $sunsetxml      = $localpath.'test/data/sunset.xml';					// local copy of nginx/stat
  $run_time       = date("U");
  $exturl         = 'http://www.earthtools.org/sun/'.$sslocation.'/';				
  // $urlparams      = '01/12/4/0';
  $todaysDate     = date('d'); // day of the month ie 14 (14th)
	$todaysMonth    = date('m'); // hour of the day ie 23 (11pm-11:59)
  $urlparams      = $todaysDate.'/'.$todaysMonth.'/99/0'; 							// 4 is gmt timezone different, 0 is daylight savings option
  $exturl         = $exturl.$urlparams;

	function getCurlData2($url)
	{
     $ch = curl_init($url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
     $contents = curl_exec($ch);
     curl_close($ch);
     return $contents;
	}
	    
  function getNode($nodeName,$chunk)
	{     
  	$openTag = '<'.$nodeName.'>';
  	$closeTag = '</'.$nodeName.'>'; 
  	$nodeTrash = stristr($chunk,$openTag);						// grab everything after the opening tag
  	$nodeContents = stristr($nodeTrash,$closeTag,true);	// drop everything after the closing tag 
  	// mixed str_replace ( mixed $search , mixed $replace , mixed $subject [, int &$count ] )
  	$nodeClean = str_replace($openTag,'',$nodeContents);  
  return $nodeClean;
 }
  
  if (file_exists($sunsetxml)) 
	{
		//echo ('local file found...<br />');
		$time_of_last_cache = date("U",filemtime($sunsetxml));
  	//echo ('$sunsetxml tlm: '.$time_of_last_cache.'<br />');
  	$time_diff = intval($run_time - $time_of_last_cache);
    //echo ('time_diff: '.$time_diff.'s<br />');
  }
  
if ((!file_exists($sunsetxml) || ($time_diff>$scacheInterval))) 
	{
		//echo ('using remote version<br />');
 		$raw_sunset = getCurlData2($exturl);   
 		file_put_contents($sunsetxml, $raw_sunset);           // create local cache for use by GUI
	}
else 
	{
		//echo ('using local cache version<br />');
	  $raw_sunset = file_get_contents($sunsetxml); 
	}
	
//	echo ('<textarea cols=160 rows=40>');
//  echo $raw_sunset;
//  echo ('</textarea><br />');
	
 $sunrise = substr(getNode('sunrise',$raw_sunset),0,5);
 $sunset = substr(getNode('sunset',$raw_sunset),0,5); 
 
 echo (' (local sunrise '.$sunrise.' | sunset '.$sunset.')');
 
 ?>
	