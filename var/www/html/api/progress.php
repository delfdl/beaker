<?php 
// s/w transcode using php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $responseJson = array();                                               // create json response array
     $time = date('ymd-Hi');                                                // unique timestamp
     
// receive job parameters (org, job)
     
      if (isset($_GET["job"])) {$src = $_GET["job"];}
      
      if (isset($_GET["org"])) {$org = $_GET["org"];}
      
      $responseJson['job']=$src;
      
      $fullpath = '/var/www/html/api/jobs/'.$org.'/'.$src.'.log';
      if (!file_exists($fullpath)) {$responseJson['status']='job reference not found at '.$fullpath;}
      else 
      {        
        $logContents = file_get_contents($fullpath);

        
        $noPasses1 = intval(strpos($logContents,'analysed - '))+11;
        $noPasses2 = substr($logContents,$noPasses1,2);
        // echo $noPasses2;
        $noPasses=intval($noPasses2);
        if ($noPasses==0) {$noPasses=1;}
        $howmanyCompleted = substr_count($logContents,'completed');
        $responseJson['status'] = round((($howmanyCompleted / $noPasses) * 100),2).'%';
        if (ceil(intval($responseJson['status']))==100) {$responseJson['status']='100%';} 
        if ((strpos($logContents,'cancelled')) || (strpos($logContents,'abort'))) {$responseJson['status']='cancelled';$responseJson['reason']='cancelled by user';}     
        if (strpos($logContents,'unable to validate')) {$responseJson['status']='failed';$responseJson['reason']='unable to identify source';}
        if (strpos($logContents,'ffmpeg puked')) {$responseJson['status']='failed';$responseJson['reason']='transcode failed (see log for details)';}
      }
      
      
    // send json response back
     $responseString = json_encode($responseJson);
     $mimetype = "application/json";
     header("Cache-Control: public, must-revalidate");
     header("Pragma: no-cache");
     header("Content-Type: ".$mimetype);
     header("Created on: ".$time);                  // cache buster
     echo $responseString;

?>