<?php 	
ini_set ("display_errors", "1");
error_reporting(E_ALL);
$path = $_GET["analysis"];
//echo $playlistItem."<br /><br />";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Jonah - analysis engine</title>
	<link href="/jonahv1/css/main.css" type="text/css" rel="stylesheet">
   </head> 
<body>
<div id='' style='padding:5px;'><img src='/jonahv1/images/inspector.png' align='right' padding='6 6 6 6' /><br /> | VALIDATING PATH | <br />
<img src='/jonahv1/images/loading.gif' name='placeholder' id='placeholder' />	
<br /><br /><br />
<?php 
//validate path here
echo ('validating<br /><b>'.$path.'</b><br /><br />');
if (!file_exists($path)) 
	{
		echo('<li>path doesnt exist<br />');
	}
	else
	{
		echo('<li>path exists<br />');
		$testFile = $path.'/jonah.tmp';
		$fh = fopen($testFile, 'w') or die("<li>path not writable<br />");
		$stringData = "Jonah r/w test\n";
		fwrite($fh, $stringData);
		fclose($fh);
		echo('<li>path writable<br /><br />');	
		
		
$test = 'ls -m '.$path;
echo 'checking '.$test.'<br /><br />';
$test2 = exec($test);
echo '<b>'.$test2.'</b><br /><br />';
$smalltest = substr($test2,255);
//echo $smalltest;	

// foreach(glob($path, GLOB_ONLYDIR) as $dir) 

foreach(glob($path.'*', GLOB_ONLYDIR) as $dir)						// for each folder found in the 'voduploaddir' root ($i = '/home/sony' )
{
	$output = exec('ls -dl '.$dir);
  echo $output.'<br />';
}






	}
	


	
?>
</div>
<div class='disclaimer' style='text-align:right;'>Analysis by Jonah&trade; 1.0 &nbsp;&nbsp;</div></div>
</body></html>