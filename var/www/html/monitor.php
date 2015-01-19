<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <TITLE>monitoring script FDL</TITLE>
    <META NAME="description" 
    CONTENT="" />
    <META NAME="keywords" 
    CONTENT="" />

 
<link rel="stylesheet" type="text/css" href="shadowbox.css">
<script type="text/javascript" src="shadowbox.js"></script>
<script type="text/javascript">
Shadowbox.init();
</script>
<style>
 body {FONT-SIZE: 8pt;
		COLOR: #344495;
		FONT-FAMILY:  Arial;}
</style>
<link rel="stylesheet" href="jquery-ui.min.css">
<script src="external/jquery/jquery.js"></script>
<script src="jquery-ui.min.js"></script>

</head>

<body> 
<?php 
 $encoder = '10.35.64.140'; 
 $rndtime = microtime(true);
 $trueTime1 = date("F j, Y, g");
 $trueTime2 = date("i a");
 $zooTime = intval(date("g"))+4; 
 $camArray = array("White Tigers","African Lions","African Wild Dogs","standby","Meerkats","African Mixed Pole","African Mixed Hut");
 $thumb = '/hms/images/94.56.170.196-preview-900';
 
 echo ("<div style='padding:2px; border:1px solid silver;'>"); 
 echo ("&nbsp;&nbsp;&nbsp;Al Ain Zoo: <b><a href='http://".$encoder."/hms/'>".$encoder."</a></b>&nbsp; | &nbsp;".$trueTime1.":".$trueTime2."&nbsp; | &nbsp; &nbsp;Zoo Local Time = ".$zooTime.":".$trueTime2);
 
 echo ("<div style='padding:2px;'>");

 for ($x=0; $x<=6; $x++) {
	 
  if($x==3) 
  {} 
  else 
	 {
 $camPort = $x+1;
 $fullImgPath = "http://".$encoder.$thumb.$x.".png?rnd=".$rndtime."";		 
  echo ("<div style='display: inline-block;padding:1px; width:160; height:120 ; background-image: url(".$fullImgPath.")' class='ui-state-default ui-corner-all'><span style='color:white;' >".$camArray[$x]."&nbsp;(".$camPort.")</span></div>");
	 }
 }  

 echo ("</div></div>");

// 		file to grab (xml)
		$xmlUrl ="http://rtmp-qa1.projectapollo2.com:8080/stat";
// $xml = simplexml_load_file($xmlUrl); //retrieve URL and parse XML content
// echo $xml->getName();

       //  $feedUrl = 'http://www.anobii.com/rss_shelf?s=01fe251a6c442bbf8a';
        $rawFeed = file_get_contents($xmlUrl);
        $rtmpqa1 = new SimpleXmlElement($rawFeed);

        foreach ($rtmpqa1->rtmp->server->application as $rtmpinfo):
            $name = $rtmpinfo->name;
            $stream = $rtmpinfo->stream;       
            echo "<span>&nbsp; ",$name,"</span> <br/> <span> ",$stream,"</span>";
        endforeach;

 ?>
 <br /><br />
 <a href='http://rtmp-qa1.projectapollo2.com:8080/stat'>NGINX ingest stats</a><br />
 
</body> 	