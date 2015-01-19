<?
ini_set ("display_errors", "1");
error_reporting(E_ALL);
     $swatchtime = date("B");                                            // get local time for use in detecting age of files
     // $formattedtime=date("F j, Y, g:i a");
?>

<?php
 // echo $filter;
 
$hostname   = 'ottilus-dev.cslxl6qitfy8.eu-west-1.rds.amazonaws.com';                           
$username   = 'ottilus';            //
$password   = 'ottilus2013';        // 
$dbname     = 'proftpd';
$usertable  = 'file';

$dbconnection = mysql_connect($hostname,$username,$password);
@mysql_select_db($dbname) or die( "Unable to select database");
// $filter = '-1'; // -1 quarantined, 0 ready, 1 transcoding, 2 complete, 4 registered

// $query = 'SELECT * FROM ' . $usertable;
$query = 'SELECT * FROM '.$usertable.' ORDER BY file_id'; // select all items

// $result = array_reverse($result);

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
    $result = mysql_query($query);
    if($result) 
    {         
    while($row = mysql_fetch_array($result))
        {
        $hash = $row['file_hash'];
        $status = $row['file_status'];
        $hashNo =  substr($hash,strrpos($hash,'_')+1 );
        //echo ("hashNo: ".$hashNo."<br />");
        $fileName = basename($row['file_name']);
        //echo ("fileName: ".$fileName."<br />");
        if (strpos($assethash,$hashNo)) 
            {
            $sensibleName = $fileName;
            }
        }
    }
   
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
    
mysql_close($dbconnection);       
?>
</table>