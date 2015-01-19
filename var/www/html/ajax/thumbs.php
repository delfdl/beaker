<?php
 ini_set ("display_errors", "1");
 error_reporting(E_ALL);
 $currenttime = date("U");
 $rndSeed     = time();
 $trueTime1   = date("d M y, H");
 $trueTime2   = date("i");
 $zooTime     = intval(date("H"))+4; 
 $descLeg     = array("","White Tigers","African Lions","African Wild Dogs","standby","Meerkats","African Mixed pole","African Mixed hut","");
 $stale       = 'red';
 $fresh       = 'white';
 $state		    = array(); 
 include('/var/www/html/includes/miniparser.php'); // load nginx xml
 
 
echo ("<div class='fluid' style='padding:5px; border:1px solid silver'>&nbsp; &nbsp;Zoo Local Time = ".$zooTime.":".$trueTime2." GST ");
include('/var/www/html/ajax/sunset.php');         	        
echo ("</div>")
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script> 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<!--<link rel="stylesheet" type="text/css" href="/shadowbox/shadowbox.css">
<script type="text/javascript" src="/shadowbox/shadowbox.js"></script>
<script type="text/javascript">
Shadowbox.init();
</script>-->






<?php



for ($x = 1; $x <= 7; $x++) 
{
	    $channel   = 'aa-port'.$x;
      $stream    = $channel.'-monitor';
      $channelbw[$x] = 0;
      $state[$x] = 'idle';
      $stColor = 'red';
      
      // foreach exploded xml node, check bw
      foreach($chunks as $chunk)
      {
        $nodename = grabNode('name',$chunk);	
        $bw_in    = grabNode('bw_in',$chunk);
        if ($nodename==$channel)
         {
      	 $channelbw[$x] = round(($bw_in/1024)/1024,2);
      	 if ($channelbw[$x]==0) 
      			{
      				$state[$x]  = 'idle';
      				$stColor = 'red';
      			} 
      			else 
      			{
      				$state[$x]  = 'active';
      				$stColor = 'green';
      			}
      	 }
      }
     
     if ($x==4) 
     {
      // dont do owt because port4 stream doesnt exist (stream names are mapped to SDI connectors)
     } else 
     {
     	// get date last modified of image
     	// 
     	
     	$dlm = date("H:i, M d",filemtime('/var/www/html/thumbs/'.$stream.'.jpg'));
     	$timetobechecked = date("U",filemtime('/var/www/html/thumbs/'.$stream.'.jpg'));
     	$age = (($currenttime-$timetobechecked)/60);
		     	if ($age<5) // red border if thumb over 5 mins old (ie 5 attempts to grab the monitoring stream)
    		 	{
     			$bColor = $fresh;
     			// 
     			} 
     				else 
     			{
     			// check xml for existance of stream with publisher client connection
     			$bColor = $stale;
     			} 
     	$fullImgPath = '/thumbs/'.$stream.'.jpg?rndSeed='.$rndSeed;		
  		
  		//echo ('<img src="/thumbs/'.$stream.'.jpg?rndSeed='.$rndSeed.'" id="'.$stream.'" style="margin:2 2 2 2;border:2px solid '.$bColor.'"  title="'.$stream.'- snapshot: '.$dlm.'" class="ui-state-default ui-corner-all tipN" data-cycle-title="aa-port'.$x.' - '.$dlm.'" data-cycle-desc="'.$descLeg[$x].'" alt="HOORAY!!" />');
  		
  		echo ("&nbsp;<div style='display:inline-block; padding:1px; background-size: 100% 100%; background-image:url(".$fullImgPath."); border:1px solid ".$bColor."' class='ui-corner-all'><div style='color:white;padding:4x;width:512px;background-color:black;opacity:0.5;'>&nbsp;<img src='/images/".$state[$x].".gif' align='absmiddle' style='vertical-align:middle' />&nbsp;".$descLeg[$x]."&nbsp;(SDI: ".$x.") &nbsp; &nbsp; <span style='float:right;opacity:0.95;'>Updated: ".$dlm." &nbsp;</span></div>");
  		
  		echo("&nbsp;<span style='color:".$stColor."'>&nbsp;".$state[$x]."&nbsp;</span><br /><a href='/fdl/latencylarge.php?url=".urlencode($channel)."' style='border:none;' title='watch ".$channel." live' class='lightbox tipN' target='popup' rel='shadowbox[Mixed];height=740;width=1280'><img src='/images/trans.gif' width='504' height='232' rel='lightbox' style='border:none;' title='".$channel." at ".$channelbw[$x]." mb/s' /></a><br /><span style='color:white; opacity:0.6; border:1px solid silver; padding:4px;'>".$channel." bandwidth: <span style='font-size:12pt; padding:2px; font-weight:bold;'>".$channelbw[$x]." mb/s</span></span></div>");
     
     }
}
// curl genthumbs.php


?>
<script>(jQuery);</script>
<script type="text/javascript">
//Shadowbox.init();
</script>
