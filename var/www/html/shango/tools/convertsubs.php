<?php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $srt = $_GET["srt"];
     
    $fullpath = "/mount/wing/vod/subs/".$srt.".srt";
    $outputdfxp = "/mount/wing/vod/subs/".$srt.".dfxp";
    $outputismt = "/mount/wing/vod/subs/".$srt.".ismt"; 
    
    // Access-Control-Allow-Origin: *  
     
if (!file_exists($fullpath))
{
    echo ("Error: '<i>".$fullpath."</i>' not found, halting operation<br />");   
} 
else 
{
    echo ("Processing: '".$srt.".srt' found...<br /><br />");
    $dfxpCommand = "mp4split -o '".$outputdfxp."' '".$fullpath."'";
   // echo ("debug: exec(".$dfxpCommand.")<br />");
    $success = exec($dfxpCommand);
    if (!file_exists($outputdfxp)) 
        {
          echo ("Error, DFXP not created, halting operation<br />");
        }
    else
    {
        echo ("DFXP created - ".$outputdfxp."<br /><br />");
        $ismtCommand = "mp4split -o '".$outputismt."' '".$outputdfxp."'";
        // echo ("debug: exec(".$ismtCommand.")<br />");
        $success = exec($ismtCommand);
        if (!file_exists($outputismt)) 
        {
          echo ("Error, ISMT not created<br />");
        } 
        else 
        {
          echo ("ISMT created - ".$outputismt."<br /><br />");  
        }
    }
} // end file not found       
?>