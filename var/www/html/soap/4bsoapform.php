<?php    
ini_set ("display_errors", "1");
error_reporting(E_ALL);
     $swatchtime = date("B");                                            // get local time for use in detecting age of files
     // $formattedtime=date("F j, Y, g:i a");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>Jonah - soap test</title>
    <link href="/jonahv1/css/main.css" type="text/css" rel="stylesheet">
   </head>
   <? include ('/var/www/jonahv1/inc/jquery.htm'); ?>
<body>
<table>
<tr><td> &nbsp;FILE </td><td> &nbsp;UPLOADED </td><td> &nbsp;FILESIZE (Mb) </td><td> &nbsp;QUEUE </td><td> &nbsp;JOB No. </td><td> &nbsp;ruby&nbsp;</td><td>&nbsp;verify </td></tr>
<?
    
$dirToCheck='/home/secure/';
$i=0;
 
     $filesfound = glob($dirToCheck."*.mp4");                         // get all files under watchfolder with a wildcard extension. (change for specific filetypes)

     foreach($filesfound as $filefound)                                 // repeat for each file found in the watchfolder
                                                                        // repeat for each .isml file found in the watchfolder
    {
        $i++; // used for pretty row colours
    // $fullpath = $dirToCheck.$filefound;
    //echo ($filefound);
    $dlm = date("F d", filemtime($filefound)).", ".date("H:i", filemtime($filefound));  
    $fileSz = intval(filesize($filefound)/1024/1024);
    $assethash1 = str_replace("/home/secure/","",$filefound);
    $assethash = str_replace(".mp4","",$assethash1);
    $tmphash = strstr($assethash, '_',true);
      
    echo ("<form name='firingonallcylinders".$assethash."' class='adminini' action='/soap/4bsoapcall.php' method='POST'><tr><td> ".$filefound." </td><td> ".$dlm." </td><td> ".$fileSz."</td><td>

<input type='hidden' name='jobname' value='manual_".$tmphash."_".$swatchtime."' />
<input type='hidden' name='presetid' value='muse4b' />    
<input type='hidden' name='inputfilename' value='file://192.168.0.6/secure/".$assethash.".mp4' />
<input type='hidden' name='outputfilename' value='file://nasa/wing/vod/".$assethash."' />
<input value='SUBMIT JOB' type='submit' action='/soap/4bsoapcall.php' />
   </td><td><div id=''></div></td><td>recreate 
    <a href='http://live1.ot2.tv:9292/vod/nodrm/".$assethash."/%2Fmount%2Fwing%2Fvod%2F".$assethash."'>".$assethash."</a> ism
    </td><td><a href='/players/osmf.php?streamURL=http://vod1.ot2.tv/vod/".$assethash."/".$assethash."_sd.ism/".$assethash."_sd.f4m' rel='shadowbox[Mixed];width=655;height=375'>playback</a>
	</td></tr></form>");    
    }
       
?>
</table>


<div class='disclaimer' style='text-align:center;'>Del | USPGUI&trade; Engine 1.0 &nbsp;&nbsp;</div></div>
</body></html>