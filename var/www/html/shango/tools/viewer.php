<?php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
    
      if (isset($_GET["srt"])) {$srt = $_GET["srt"];} 
        else {$srt = 'notset';}  
       // echo "srt: ".$srt;                                 // use basename of src if not provided
        
       if (isset($_GET["log"])) {$log = $_GET["log"];} 
        else {$log = 'notset';}  
        //echo "log: ".$log;                                 // use basename of src if not provided       
    
    // $srt = $_GET["srt"];
                                                             
if ($log='notset')
    {
     $info = file($srt);                              
     foreach ($info as $infoArray)
         {
             $infoArray = str_replace(PHP_EOL, '', $infoArray);
             //echo $info_line;
             if (strlen($infoArray)==1) 
             {
                echo ("\n "."<br />".$infoArray);
             } 
             else 
             {
                echo ("\n "."<br />| ".$infoArray);
             } 
          }
     }
     
     else 
     {
     $info = file($log);                              
     foreach ($info as $infoArray)
         {
             $infoArray = str_replace(PHP_EOL, '', $infoArray);
             //echo $info_line;
             if (strlen($infoArray)==1) 
             {
                echo ("\n "."<br />".$infoArray);
             } 
             else 
             {
                echo ("\n "."<br />| ".$infoArray);
             }
          }    
      }

       
?>