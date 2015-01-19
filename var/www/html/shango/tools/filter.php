<?php
 $filter=$_GET["q"];
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
$query = 'SELECT * FROM '.$usertable.' WHERE file_status="'.$filter.'" ORDER BY file_id DESC limit 50'; // select all quarantined items
$result = mysql_query($query);
// $result = array_reverse($result);
echo ('<table style="width:95%">');
echo ('<tr class="tabhead-transcode"><td> File ID </td><td> Source Path </td><td> Status </td><td> View </td><td> Ingested </td></tr>');
if($result) {
    
    while($row = mysql_fetch_array($result)){
        $fileId = $row['file_id'];
        $fileName = $row['file_name'];
        $fileStatus  = $row['file_status'];
        $fileCreated = $row['file_created'];
        $ingestInception = date("F j, H:i T",strtotime($fileCreated));        
        echo ('<tr><td> '.$fileId.' </td><td> '.$fileName.' </td><td> '.$fileStatus.' </td>
        <td><a href="/tools/resubmit.php?id='.$fileId.'">retry</a> </td><td>'.$ingestInception.'</td></tr>');
        // echo $file_id.': '.$filename.'<br />';
    }
}
else 
    {
    echo ('<tr><td colspan="5"> no results found </td></tr>');
  //  echo "Database NOT Found ";
    }
echo ('</table>');   
mysql_close($dbconnection);
?>