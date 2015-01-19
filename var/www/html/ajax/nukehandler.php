<?php
echo ('yey');
  $localpath      = '/var/www/html/';
  $localnuke      = $localpath.'test/data/nuke.json';					// local copy of nginx/stat 
  $nuke = array(); 
  
  $nukeopt = file_get_contents($localnuke);
  $nuke    = json_decode($nukeopt,true);
  
  if ($nuke['trigger']=='1')
   {$nuke['trigger']='0';}
  else 
   {$nuke['trigger']='1';}
 
	$nukeopt = json_encode($nuke);
 	file_put_contents($localnuke, $nukeopt);
 

?>