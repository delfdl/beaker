<?php 
    ini_set ("display_errors", "1");
    error_reporting(E_ALL);
    
    
      $load = sys_getloadavg();
      $memInfo = '/proc/meminfo';
      $mem_array = file($memInfo);
      foreach ($mem_array as $mem_key)
      {
        // echo ("<br />".$mem_key);
        if (strpos($mem_key,"emTotal")) 
             {  
                $cerb_MT = str_replace("MemTotal: ","",$mem_key);
                $cerb_MT = (str_replace(" kB","",$cerb_MT)) ;
                $cerb_MT = round($cerb_MT/1024/1024,2); // memory in Gb to 3 decimal places
                //echo "cerb_mt: ".$cerb_MT."<br />";
                $memInstalled = ceil($cerb_MT); 
                //echo ("cerb_mi: ".$memInstalled." Gb found <br />");
             }
             
         if (strpos($mem_key,"emFree")) 
             {  
                $cerb_MF = str_replace("MemFree: ","",$mem_key);
                $cerb_MF = (str_replace(" kB","",$cerb_MF)) ;
                $cerb_MF = round($cerb_MF/1024/1024,2); // memory in Gb to 3 decimal places
                //echo "cerb_mf: ".$cerb_MF."<br />";
             }    
      }

      $memPercent = ceil(100-(($cerb_MF/$cerb_MT)*100)); // get percent memFree, remove from 100 to get mem used    
?>
<div style='width:98%; border:1px solid red; padding:3px; margin:5px; '><span style='text-align:right;padding:5px;margin:3px;'>INGEST2 CPU: <b><?php  echo ($load[0]); ?>%</b> | Memory usage: <?php  echo ($cerb_MF.' / '.$memInstalled.'Gb &nbsp;'); ?> </span></div><br />     
<div style='padding:3px';'>
<?php
        $soapUrl = "http://head1.ot2.tv/balancerSOAP";
        $xml_post_string = '
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns="urn:envivio:balancer:1.0">
  <soap:Header/>
  <soap:Body>
    <getAllResources/>
  </soap:Body>
</soap:Envelope>'; // 
         $headers = array(
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "SOAPAction: http://head1.ot2.tv/balancerSOAP/getAllResources", 
                        "Content-length: ".strlen($xml_post_string),
                    ); //SOAPAction: your op URL

            $url = $soapUrl;

            // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // converting
            $response = curl_exec($ch); 
            curl_close($ch);

            // converting
            //$response1 = str_replace("<resources>","",$response);
            
            $response1 = strstr($response,'<resources>');
            $response2 = strstr($response1,'</resources>',1);
            $resourcecount = substr_count($response2,'name=');
            echo ('H/W transcode nodes : <b>'.$resourcecount.'</b><br />'); 
                       
            $count = explode('</resource><resource>',$response2);

            foreach($count as $resource)
            {
              // echo '<textarea cols=100 rows=3>'.$resource.'</textarea>';
               $encname2 = strstr($resource,'name=');
               $encname3 = str_replace('name="','',$encname2);
               $encname = strstr($encname3,'"',1);
               $encip2 = strstr($resource,'url=');
               $encip3 = str_replace('url="','',$encip2);
               $encip = strstr($encip3,'"',1);
               
               echo ("<div>".$encname." | ".$encip."</div>");
              
            }
            ?>
<br /></div>   
<?php     
include ('/var/www/html/api/jonahlib.php'); 
include ('/var/www/html/api/processes.php');
 
$vodlogDir = '/var/www/html/api/jobs';

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
            echo ("<br /><table class='jonahtable' width='99%'><tr class='tabhead-upload'><td class='td-path'> Client </td><td class='td-name'> Job ref </td><td class='td-size'> &nbsp; </td><td class='td-timestamp'> &nbsp; </td><td class='td-duration'> Action </td><!--<td class='td-ffmpeg'>[debug]</td>--><td class='td-video'> View log </td><td class='td-audio'> Complete </td><td class='td-status'> Status </td><td class='td-icon'> Output </td><td> Ingest (UTC) </td><td> Time taken </td><td> Preview </td></tr>");     
         
             $filecount = count(glob($newwatchfolder."*.*"));
            // echo (" | ".$filecount." files | <br />");    
             $filesfound = glob($newwatchfolder."*.*");                         // get all files under watchfolder with a wildcard extension. (change for specific filetypes)
             $wilmaflag = 0;                                                    // wilma = flag if timestamp recorded for ffmpegprocess finishing
             
             foreach($filesfound as $filefound)                                        // repeat for each file found in the watchfolder
             {   
              $path_info = pathinfo($filefound);
              $filename = $path_info['filename']; // get file extension
              $logContents = file_get_contents($filefound);
              
              $pieces = explode('|',$logContents);                        // we put this in the log specifically for this occasion. specially, like. 
              //echo $pieces[0].' # '.$pieces[1].' # '.$pieces[3].'<br />';
              if (isset($pieces[1])) 
              {
                  $playbackURL = $pieces[1];
                  $src1 = explode('*',$pieces[0]);
                  $src2 = strrchr($src1[0],'/');
                  $src3 = substr($src2,1);
              } 
              else {$playbackURL = 'undefined URL';}  
              if (isset($pieces[2])) {$outputDir   = $pieces[2];} else {$outputDir = 'invalid';} 
             
            foreach ($pieces as $piece) 
             {
                if (strpos($piece,'accepted')) 
                {
                 $flintstone = getlogTime($piece);
                }
                if (strpos($piece,'refTimestamp')) 
                {
                  $started = substr_count($logContents,'starting');
                  $completed = substr_count($logContents,'completed');
                  if ($started>$completed) 
                  {
                    $wilma = microtime();
                    $wilmaflag = 0;
                  } 
                  else 
                  {
                    $wilma1 = getlogTime($piece); 
                    $wilmaflag = 1;
                  }
                } 
                else 
                {
                  $time = microtime();
                  $time = explode(' ', $time);
                  $wilma = $time[1] + $time[0];
                  
                }
            }     
            
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
              if (strpos($logContents,'ancelled')) {$perCent='cancelled'; $colorid='statusrejected';}
              
             // $srcfilename = 'src filename goes here';
             $srcfilename = $src3;
              // work out src filename
                 
                 echo ("<tr class='".$colorid."'><td>".$client."</td><td colspan='3'>".$filename."<br />(".$srcfilename.")</td><td>");
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
                    //echo ($outputDirfull);
                    echo (" - ");    
                 }                    
                 echo ('</td><td>');
              
              // add ingest time, time taken here
                 $babyflint = date('d M Y, H:i', $flintstone);
                 echo ($babyflint.'</td><td>');
              
              if ($wilmaflag==1) {$wilma = $wilma1;}
              
                   $total_time = round(($wilma - $flintstone), 4);
                   $hrs = intval($total_time/3600);
                   $mins = intval($total_time/60);
                   $secs = round($total_time-($mins*60),0);
                 echo $mins.'m '.$secs.'s<br />'; 
                 // echo $started.'/'.$completed; 
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