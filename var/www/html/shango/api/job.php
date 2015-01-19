<?php
// s/w transcode using php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $responseJson = array();                                               // create json response array
     $time = date('ymd-Hi');                                                // unique timestamp
     
// receive job parameters (src, tmpname, org, optional hqflag, option debug, option rewrap)
     
      if (isset($_GET["src"])) 
      {
          $src = $_GET["src"];
          if (!file_exists($src)) {$src='invalid';}
      } 
        else {$src = 'invalid';}                                            // fail if not a valid path
 
      if (isset($_GET["tmpname"])) {$tmpname = $_GET["tmpname"];} 
        else {$tmpname = basename($src);}                                   // use basename of src if not provided

      if (isset($_GET["org"])) {$org = $_GET["org"];} 
        else {$org = 'invalid';}                                            // need to do validation also
        
      if (isset($_GET["channel"])) {$channel = $_GET["channel"];} 
        else {$channel = 'invalid';}                                        // need to do validation also
      
      if (isset($_GET["hqflag"])) {$hqflag = $_GET["hqflag"];} 
        else {$hqflag = 0;}  // 0 = default, single pass. 1 = 1 reference pass per preset. 2 = 1 reference pass per profile
        
      if (isset($_GET["debug"])) {$debug = $_GET["debug"];} 
        else {$debug = 0;}  // 0 = default, 1=verbose reporting  
        
      if (isset($_GET["rewrap"])) {$rewrap = $_GET["rewrap"];} 
        else {$rewrap = 0;}  // 0 = default(transcode), 1 = just rewrap, resubmit to queue  

// return job reference
     $formattedtime = date('ymd-Hi');        // eg 130801-1403
     $uniqueRef = $formattedtime.'-'.$tmpname;
     
     $responseJson['job']  = $uniqueRef;
     $responseJson['timestamp'] = $time;
     $responseJson['src'] = $src; 
     $responseJson['tmpname'] = $tmpname; 
     $responseJson['org'] = $org; 
     $responseJson['hqflag'] = $hqflag;
     $responseJson['rewrap'] = $rewrap;
     $responseJson['channel'] = $channel;    
 
    if (($src=='invalid') || ($tmpname=='invalid') || ($org=='invalid')) 
         {$responseJson['response'] = 'rejected';}
    else {$responseJson['response'] = 'accepted';} 
    
// create logfile using job reference
    $path = '/var/www/api/jobs/'.$org.'/'; // should already exist
    // mkdir($path, 0755);
    $logFile = $path.$uniqueRef.'.log';
    $handle = fopen($logFile, 'w');
    $newtime = date('d/m/y H.i:s');
    $playbackchunk = $org.'/'.$channel.'/'.$tmpname.'/';
    $data = '['.$newtime.'] '.$responseJson['response'].' by job.php -'.$src.' to /mount/wing/vod/'.$playbackchunk.' (|http://vod1.ot2.tv/vod/'.$playbackchunk.$tmpname.'.ism/'.$tmpname.'.f4m|'.$playbackchunk.'|)';
    fwrite($handle, $data);
    fclose($handle);
    chmod($logFile, 0777); 
    
    
    // send json response back
     $responseString = json_encode($responseJson);
     $mimetype = "application/json";
     header("Cache-Control: public, must-revalidate");
     header("Pragma: no-cache");
     header("Content-Type: ".$mimetype);
     header("Created on: ".$time);                  // cache buster
     echo $responseString;
     
// pass src file, working tempname, organisation, hqflag (optional) to transcode.php

    if ($responseJson['response']=='accepted') 
    {

        $ch = curl_init('http://ingest2.ot2.tv/api/transcode.php'); 
        $timeout = 5; 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $responseString);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Content-Length: ' . strlen($responseString))                                                                       
        );                                                                                                                   
        $result = curl_exec($ch); 
        curl_close($ch);
    } 
    else
    {
        $result = ' | NOT PROCESSED';    
    }

   if ($debug==1) {echo $result;}
    
?>