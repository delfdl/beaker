<html>
<head></head>
<body>
	<?php
// variables

     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $run_time = date("U");
     echo ('local time: '.$run_time.'<br />');

		
// functions

		function getCurlData($url)
{
     $ch = curl_init($url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
     $contents = curl_exec($ch);
     curl_close($ch);
     return $contents;
}
// code start

		$exturl = 'http://rtmp-qa1.projectapollo2.com:8080/stat';
		echo ('using remote version<br />');
 		$raw_premier = getCurlData($exturl);   

 echo ('<textarea cols=160 rows=40>');
 echo $raw_premier;
 echo ('</textarea><br />');
 
 ?>
 </body>
	</html>