<?php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $srt = $_GET["srt"];
$srt = urlencode($srt);
$url = 'http://live.ot2.tv/tools/convertsubs.php?srt='.$srt; 
    $ch = curl_init(); 
    echo ("initiating ".$url."<br />");
    $timeout = 5; 
    curl_setopt($ch,CURLOPT_URL,$url); 
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
    $response = curl_exec($ch); 
    curl_close($ch); 
 
echo($response);    

?>