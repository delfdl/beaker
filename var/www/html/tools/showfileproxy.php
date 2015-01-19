<?php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $hash = $_GET["hash"];
//$hash = urlencode($hash);
$url = 'http://live1.ot2.tv/tools/api/showfiles.php?hash='.$hash; 
    $ch = curl_init(); 
    // echo ("initiating ".$url."<br />");
    $timeout = 5; 
    curl_setopt($ch,CURLOPT_URL,$url); 
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
    $response = curl_exec($ch); 
    curl_close($ch); 
 
echo($response);    

?>