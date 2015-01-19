<?php
    ini_set ("display_errors", "1");
    error_reporting(E_ALL);
    include ('/var/www/api/jonahlib.php');         // functions
    
    if (isset($_GET["job"])) {$job = $_GET["job"];} 
        else {$job = 'invalid';}
        
    if (isset($_GET["org"])) {$org = $_GET["org"];} 
        else {$org = 'invalid';}
            
    $logFile = '/var/www/api/jobs/'.$org.'/'.$job.'.log';    
    transcodeLog($logFile,'abort call received');    
echo $job.' marked for aborting - active encode tasks will complete';    
?>
