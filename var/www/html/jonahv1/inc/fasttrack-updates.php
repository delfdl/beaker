<?php  $epgid = $_GET["epgid"];

	$jsonFile = '/var/www/html/jonahv1/queue/fasttrack/'.$epgid.'.json';

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
	}
	$newJson		= json_encode($ftJson);
	file_put_contents($jsonFile, $newJson);
	//$fullJson
	return $fttimestamp;
	
?>