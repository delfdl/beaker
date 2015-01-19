<?
ini_set ("display_errors", "1");
error_reporting(E_ALL);
     $swatchtime = date("B");                                            // get local time for use in detecting age of files
     // $formattedtime=date("F j, Y, g:i a");
?>

<table style='width:95%'>
<tr class='tabhead-transcode'><td> &nbsp;FILE </td><td> &nbsp;UPLOADED </td><td> &nbsp;FILESIZE (kb) </td><td> &nbsp;LANGUAGE </td><td> &nbsp;ALIAS </td><td> &nbsp;CONVERT&nbsp;</td><td>&nbsp;LINK </td></tr>

<?
    
$dirToCheck='/home/secure/';
$i=0;
 
     $filesfound = glob($dirToCheck."*.srt");                         // get all files under watchfolder with a wildcard extension. (change for specific filetypes)

     foreach($filesfound as $filefound)                                 // repeat for each file found in the watchfolder
                                                                        // repeat for each .isml file found in the watchfolder
    {
        $i++; // used for pretty row colours
    // $fullpath = $dirToCheck.$filefound;
    //echo ($filefound);
    
    $dlm = date("F d", filemtime($filefound)).", ".date("H:i", filemtime($filefound));  
    $fileSz = intval(filesize($filefound)/1024);
    $assethash1 = str_replace("/home/secure/","",$filefound);
    $assethash = str_replace(".srt","",$assethash1);
    $sensibleName='';
   
   echo ("<tr>
   <td> ".$filefound." &nbsp | &nbsp; <a onClick=\"changeURL('/tools/viewer.php?srt=".$filefound."')\" href='#'>view</a></td>
   <td> ".$dlm." </td>
   <td> ".$fileSz."</td>
   <td>-</td>
   <td><span>".$sensibleName."</span></td>  
   <td><a onClick=\"changeURL('/tools/convertproxy.php?srt=".$assethash."')\" href='#'>convert</a></td>
   <td><a onClick=\"changeURL('/tools/associatesubs.php?srt=".$filefound."')\" href='#'>associate</a></td>
   </tr>");  
      
    } // end for each file found
    
// mysql_close($dbconnection);       
?>
</table>