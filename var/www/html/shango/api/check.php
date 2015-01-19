<?php
    ini_set ("display_errors", "1");
    error_reporting(E_ALL);
$vodlogDir = '/var/www/api/jobs';

foreach(glob($vodlogDir.'*', GLOB_ONLYDIR) as $i)                        // for each folder found in the 'voduploaddir' root ($i = '/home/sony' )
    { 
    //echo "voduploaddir: ".$voduploaddir." ";    
    //echo $i." | ";
    $watchfolder=$i."/";
    //echo ("watch folder: ".$watchfolder." | ");                          // add trailing slash to folder
    $client = str_replace($vodlogDir,"",$i);
    //echo ($client." ");  
    $filecount = count(glob($watchfolder."*.*"));
    //echo (" | ".$filecount." files | "); 
    //echo glob($watchfolder."*.*")."<br />";
    
    foreach(glob($watchfolder.'*', GLOB_ONLYDIR) as $i)                    // for each folder found in the 'voduploaddir' root ($i = '/home/sony' )
        {
            // echo ("i: ".$i." | "); 
            echo ('<br />');                                      // add trailing slash to folder
        $client = strtolower(str_replace($watchfolder,"",$i));
        // echo ("client: ".$client." <br />"); 
        $newwatchfolder = $watchfolder.$client."/";
        //echo "newwatchfolder".$newwatchfolder."<br />";
    
    
        if (glob($newwatchfolder."*.*") != false)                           // if files in upload folder
         {
            echo ("<br /><table class='jonahtable' width='100%'><tr class='tabhead-upload'><td class='td-path'> Client </td><td class='td-name'> Job ref </td><td class='td-size'> &nbsp; </td><td class='td-timestamp'> &nbsp; </td><td class='td-duration'> Action </td><!--<td class='td-ffmpeg'>[debug]</td>--><td class='td-video'> View log </td><td class='td-audio'> Complete </td><td class='td-status'> Status </td><td class='td-icon'> Output </td><td> Preview </td></tr>");     
         
             $filecount = count(glob($newwatchfolder."*.*"));
            // echo (" | ".$filecount." files | <br />");    
             $filesfound = glob($newwatchfolder."*.*");                         // get all files under watchfolder with a wildcard extension. (change for specific filetypes)
             
             foreach($filesfound as $filefound)                                        // repeat for each file found in the watchfolder
             {   
              $path_info = pathinfo($filefound);
              $filename = $path_info['filename']; // get file extension
              $logContents = file_get_contents($filefound);
              
              $pieces = explode('|',$logContents);                        // we put this in the log specifically for this occasion. specially, like. 
              if (isset($pieces[1])) {$playbackURL = $pieces[1];} else {$playbackURL = 'undefined URL';}  
              if (isset($pieces[2])) {$outputDir = $pieces[2];} else {$outputDir = 'invalid';}     
            
              $noPasses1 = intval(strpos($logContents,'analysed - '))+11;
              $noPasses2 = substr($logContents,$noPasses1,2);
                       // echo $noPasses2;
              $noPasses=intval($noPasses2);
              if ($noPasses==0) {$noPasses=1;}
              $howmanyCompleted = substr_count($logContents,'completed');
              $perCent = round((($howmanyCompleted / $noPasses) * 100),2).'%';
              $colorid = 'statusactive';
              
              if ($perCent=='100%') {$colorid='statusdone';}
              
              if (strpos($logContents,'rejected')) {$perCent='rejected'; $colorid='statusrejected';}
                 
                 echo ("<tr class='".$colorid."'><td>".$client."</td><td colspan='3'>".$filename."</td><td>");
                 if ($perCent=='100%') 
                 {
                     echo (" <a href='/shango/player.php?url=".$playbackURL."'>playback</a> ");
                 } 
                 else 
                 {
                     echo (" <a href='/api/abort.php?job=".$filename."&org=".$client."'>abort job</a> ");
                 }
                 
                 echo ("</td><td><a onClick=\"changeURL('/tools/viewer.php?srt=".$filefound."')\" href='#'>view</a>
                 </td><td>".$howmanyCompleted."/".$noPasses."</td><td>".$perCent."</td></td><td>");
                 
                 $outputDirfull = '/mount/wing/vod/'.$outputDir;
                 $outputfilecount = count(glob($outputDirfull."*.*"));
                 if ($outputfilecount>1) 
                 {
                 // echo (" | ".$filecount." files | <br />");    
                 $outputfilesfound = glob($outputDirfull."*.*");
                 $totalCount = 0;
                    foreach($outputfilesfound as $outputfilefound)
                    {
                      $totalCount=$totalCount+filesize($outputfilefound);  
                    }   
                 echo (round($totalCount/1024/1024,2).'mb');
                 }
                 else 
                 {
                    echo ($outputDirfull);
                    echo ("no output found");    
                 }                    
                 echo ('</td><td>');
                 $thumbsfound = glob($outputDirfull."*_thumb.jpg");
                 // echo count($thumbsfound);
                 foreach($thumbsfound as $thumbfound)
                 {
                   $webPath = str_replace('/mount/wing/','http://vod1.ot2.tv/',$thumbfound);  
                   echo ("<img class='ui-state-default ui-corner-all' src='".$webPath."' />");  
                 }
                 
                 echo ("</td></tr>");
             }
           echo ("</table>");  
      }
      else {echo('no jobs listed');}
     }
    } 
?>
