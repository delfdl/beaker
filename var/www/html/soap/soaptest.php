<?php    
    ini_set ("display_errors", "1");
    error_reporting(E_ALL);
    $swatchTime  = date("B");
    $currentTime = date("U");                                            // get local time for use in detecting age of files
    $howmanyHrs = '48';                                                 // show uploads fresher than 48 hrs
    $ageFilter  = $howmanyHrs*60*60;                                                
    // $formattedtime=date("F j, Y, g:i a");
	
 function thumbGen($inputVOD,$channel,$thumbTime,$offset)
 	{  
 		$selectI =" -vf select='eq(pict_type\,I)'";
 		// $selectI = "";
		$fireandforget = "> /dev/null 2>/dev/null &" ; // add to exec (ffmpeg) call to make asynchronous (mmmm!)
		$fireandforget = '';
 		$ffmpegThumb = "ffmpeg -itsoffset -".$offset." -i ".$inputVOD." -vframes 1 -s 160x90 ".$selectI." -ss ".$thumbTime." -f image2 '/var/www/uspgui/preview/".$channel.".jpg'".$fireandforget; // kick off thumbgen
		echo ("ffmpegThumb: ".$ffmpegThumb."<br />");
		$tmp = exec($ffmpegThumb);
		echo $tmp."<br />";
		//if (!exec($ffmpegThumb))
			//{$success=0;} 
		//else 
			//{$success=1;}
			//echo ("success: ".$success."<br />");
    return $tmp;
 	}
 
 function getDuration($inputVOD)
	{
	$durCommand = "ffmpeg -i ".$inputVOD." 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//";
	$tmp = exec($durCommand);
	echo "Duration: ".$tmp."<br />";
	return $tmp;	
	}
    
 function showInfo($inputVOD,$hash,$filename)
 {
        $longfilename=str_replace(" ","_",$filename);     
        $inspect_file='/var/www/qc/'.$hash."_".$longfilename.".txt";                              // full path path to 'ffmpeg -i' output (e.g. '/var/www/jonahv1/txtout/shakira liar.txt')
        $ffmpeg_inspect="ffmpeg -i '".$inputVOD."' 2> ".$inspect_file;          // ffmpeg command (e.g. 'ffmpeg -i '/home/sony/Shakira Liar.mpg' '/var/www/jonahv1/txtout/sony-shakira_liar.txt'
        if (!file_exists($inspect_file)) 
        {
             $ffinspect = exec($ffmpeg_inspect); // only run ffmpeg -i if output file doesnt already exist
        } else {
           // $ffinspect = exec($ffmpeg_inspect); // only run ffmpeg -i if output file doesnt already exist
        }
     $uhuh = $hash."_".$longfilename.".txt";
     return $uhuh;
 }   	
	
?>
   <? include ('/var/www/jonahv1/inc/jquery.htm'); ?>


<table style='width:95%'>
<? 

$response = file_get_contents('http://jonah.dev.ottilus.in/list/queue/5');

$test1 = strstr($response,'[',false);
$test1 = substr($test1,2);
// echo ("TEST1: <div style='border:1px solid silver'>".$test1."</div><br /><br />");
$rightDelimiter='],"totalItems';
$test2 = strstr($test1,$rightDelimiter,true);
$test2 = substr($test2, 0, -1);

// echo ("TEST2: <div style='border:1px solid red'>".$test2."</div><br /><br />"); 
$explodeID="},{";                                                                    
    $explodeIDcount=substr_count($test2,$explodeID);
    $queueExplode=explode($explodeID,$test2);
    //for each $queueExplode as $queueVod

echo (" 
        <tr class='tabhead-transcode'><td>Key</td><td>&nbsp;Alarm</td><td>Filename</td><td>Owner</td><td>Uploaded </td><td> DISK </td><td>Status</td><td>&nbsp;ACTION</td><td>&nbsp;Inspect | 
        QC&nbsp;</td></tr>
");
    $queueExplodeRev=array_reverse($queueExplode);
    foreach($queueExplodeRev as $queueVod)
    {
        $reformed='{'.$queueVod.'}';
        $qItem = json_decode($reformed,true);
        //echo $reformed."<br />";
        
       // key
       $fileID = $qItem['file_id']; 
       
       // hash
       $fileHash = $qItem['file_hash'];
       $hashNo = substr(strrchr($fileHash,"_"),1); // eg b2a8c8008ef8b6add5a985aaebdace2e
	   $hashID = str_replace('_'.$hashNo,'',$fileHash); // eg ott56
	   $tmpNo = str_replace('ott','',$fileID); // eg 56
	   $newNo = $tmpNo+3; // eg 59
	   $newHash = 'ott'.$tmpNo.'_'.$hashNo; // crappy workaround for hash being +3
       
       //owner
       $userName = $qItem['file_username'];
       
       //date
       $fileCreated = $qItem['file_created'];
       $newDate = date("U", strtotime($fileCreated));
       $currentAge = $currentTime - $newDate;       
       $ingestInception = date("F j, H:i T",strtotime($fileCreated)); 
       
       //name
       $longfileName = $qItem['file_name'];
       $fileName = substr(strrchr($longfileName,"/"),1); // everything after last slash but without the slash
       
       //status
       $fileStatus = $qItem['file_status'];
       switch ($fileStatus) 
       {
        case -1: $adjStatus = 'Quarantined (-1) ';
        break;
        case 0: $adjStatus = 'Queued (0)';
        break;
        case 1: $adjStatus = 'Transcoding (1)';
        break;
        case 2: $adjStatus = 'Transcode Complete (2)';
        break;       
        case 3: $adjStatus = 'Job completed (3)';
        break;
        case 4: $adjStatus = 'Registered (4)' ;
        break; 
        case 5: $adjStatus = 'Archived (5)';
        break; 
        case 6: $adjStatus = 'Marked for deletion (6)';
        break; 
       }       
   
   // check file exists in /secure/
   $fileExtension = strrchr($fileName,'.');                                  // get file extension
   $inceptFile = $qItem['file_name'];
   $inputFile = '/home/secure/'.$fileHash.$fileExtension ;                  // work out path to ingested file
   // $playoutFile = '/mount/wing/vod/'.$fileHash.'/'.$fileHash.'.ism' ;        // work out path to playout file
   $inputFile = '/home/secure/'.$newHash.$fileExtension ;    
   //$playoutFile = '/mount/wing/vod/'.$fileHash.'/'.$fileHash.'.ism' ;        // work out path to playout file
   $playoutFile = '/mount/wing/vod/otv/'.$newHash.'/'.$newHash.'.ism' ;        // work out path to playout file
   $playoutVOD = '/mount/wing/vod/otv/'.$newHash.'/'.$newHash.'_201_280000.ismv' ;        // work out path to playout file
      
  if (file_exists($inceptFile))     // does file exist in upload folder?
    {$inceptImg='state-on'; $inceptSize=(filesize($inceptFile)/1048576);} 
    else 
    {$inceptImg='state-off';$inceptSize='';}   
  if (file_exists($inputFile))      // does file exist in secure folder?
    {$inputImg='state-on'; $inputSize=(filesize($inputFile)/1048576);} 
    else 
    {$inputImg='state-off';$inputSize='';}
  if (file_exists($playoutFile))    // does file exist in playout folder?
    {$playoutImg='state-on'; $playoutSize=(filesize($playoutFile)/1048576);} 
    else 
    {$playoutImg='state-off'; $playoutSize='';}
    
    // amend $playoutFile here
    
    
    if ($currentAge<$ageFilter)  // only show entries fresher than filter date
    {
        
       echo("<tr><td>".$fileID."</td><td><img src='/jonahv1/images/job".$fileStatus.".jpg'/></td><td> &nbsp; &nbsp; <b>".$fileName."</b> | <a href='#' class='tooltip' title='$fileHash'>hash</a></td><td>".$userName."</td><td>".$ingestInception." </td><td>
       <a href='/api/thumbtest.php?src=$inceptFile&hash=$hashNo' class='tooltip' title='INGEST: $inceptFile'><img src='/jonahv1/images/".$inceptImg.".png' border='0' /></a>
       <a href='#' onClick=\"changeURL('/api/thumbtest.php?src=".$inputFile."&hash=".$hashNo."')\" 
       class='tooltip' title='SECURE: $inputFile'><img src='/jonahv1/images/".$inputImg.".png' border='0' /></a>
       <a href='/api/thumbpost.php?src=$playoutVOD&hash=$hashNo' class='tooltip' title='PLAYOUT: $playoutVOD'><img src='/jonahv1/images/".$playoutImg.".png' border='0' /></a>
       </td><td class='job".$fileStatus."'>&nbsp;".$adjStatus."</td><td><a onClick=\"changeURL('/tools/showfileproxy.php?hash=".$newHash."')\" href='#'>recreate ism</a></td><td>");
       
      //$qcpath = showInfo($inputFile,$hashNo,$fileName);
      // $qcpath='invalid';
      //$qcpath='/qc/'.$qcpath;
      // echo("<a href='#' onClick=\"changeURL('/jonahv1/inc/analyse-modal-qc.php?analysis=".$qcpath."')\" rel='shadowbox[Mixed];width=666;height=888' />details</a>");
      
       echo("<a href='#' onClick=\"changeURL('/tools/qc.php?file_id=".$newHash."')\" rel='shadowbox[Mixed];width=666;height=888' />details</a>");
       echo("</td></tr>");
    }    
     
    }

    echo ("</table>");
?>
<div style='padding:5px;color:grey;'>&nbsp;<a href='#' onClick="changeURL('/tools/watchrestart.php')">Nudge Queue &raquo;</a>&nbsp;</div>