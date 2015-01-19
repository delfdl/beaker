<?php 
	 ini_set ("display_errors", "1");
	 error_reporting(E_ALL);
	 $diskItem = $_GET["nas"];
	 // temp storing on 37.188.116.67 (rackspace)
	 include ('/var/www/html/jonahv1/inc/jonah-lib.php')	; 
	 $debug=1;																						// debug = show working paths, variables etc (not currently implemented)
	 $refreshtime=60;	 	 																	// determines how often watchfolders get prodded
	 $currenttime=date("U");															// get local time for use in detecting age of files
	 // $formattedtime=date("F j, Y, g:i a");
	 $formattedtime=date("g:i a")." GMT<br />".date("F j");
	// load below parameters from admin.json file					// suggest using mySql DB later
 
	$adminVar=file_get_contents('/var/www/html/jonahv1/admin/config.json'); 	//read admin variable from local json file (use mySql db later)
	$config = json_decode($adminVar, true);								// json decode array
	
	$voddrive = $config['voddrive'];											// set disk array used for vod storage
	$voduploaddir = $config['voduploaddir'];							// set disk path used for vod upload
	$pathtoinspect = $config['pathtoinspect'];						// ffmpeg inspection text files go here (temp folder
	$pathtoqueue = $config['pathtoqueue'];								// place files to be transcoded here
	$pathtoxml = $config['pathtoxml'];										// place associated xml here
	$allowed_video_types = $config['allowedvideotypes'];	// set allowed video types to process
	$allowed_xml_types = $config['allowedxmltypes'];			// set allowed xml suffixes
	$minstowait = $config['minstowait'];									// how long to wait for xml before queuing for transcode
	$orphanpath = $config['orphanpath'];									// path to orphans (non-media, non-xml)
	$duration='unknown   ';
	if ($diskItem=='ingest1') {$dirToCheck=$voduploaddir;}
	if ($diskItem=='wing') {$dirToCheck=$voddrive;}
	if (!$dirToCheck) {$dirToCheck='/';}                     // if no disk specified, default to '/'
	
	if (!$voddrive) {echo ("error reading config.json<br />ending script to protect file structure"); break;}	// if config.json is corrupt, terminate script
																
	$diskfree=round((disk_free_space($voddrive)/1000000000),1);			// get disk-unused in Gbytes
	$disktotal=round((disk_total_space($voddrive)/1000000000),1);		// get disk-size in Gbytes
	$diskpercent = round(($diskfree/$disktotal*100));
	$diskpercentused=100-$diskpercent;
	$diskimagetouse=intval($diskpercentused/10)*10;						// use closest disk image to capacity used (ie under 10%, 10-19%, 20-29% etc)
	
	$diskfree2=round((disk_free_space($voduploaddir)/1000000000),1);			// get disk-unused in Gbytes
	$disktotal2=round((disk_total_space($voduploaddir)/1000000000),1);		// get disk-size in Gbytes
	$diskpercent2 = round(($diskfree2/$disktotal2*100));
	$diskpercentused2=100-$diskpercent2;
	$diskimagetouse2=intval($diskpercentused2/10)*10;						// use closest disk image to capacity used (ie under 10%, 10-19%, 20-29% etc)
	
  $units = explode(' ', 'B KB MB GB TB PB');
  $SIZE_LIMIT = disk_total_space($dirToCheck);
	 
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Jonah - file ingest engine</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="/jonahv1/css/tablednd.css" type="text/css" rel="stylesheet">
	<link href="/jonahv1/css/main.css" type="text/css" rel="stylesheet">
  <script src="/jonahv1/scripts/libraries.js" type="text/javascript"></script>
  <META HTTP-EQUIV="MSThemeCompatible" Content="Yes">
  <?php  include ('/var/www/html/jonahv1/inc/jquery.htm'); ?>
</head>
<body><?php  include ('/var/www/html/jonahv1/inc/topbar.php'); ?>
<div style='padding:5px; margin:0 0 0 15;'>
<?php  $disktotal=disk_total_space($dirToCheck);
$disktotal2=round(($disktotal/1000000000),1);		// get disk-size in Gbytes
$capacity=round((disk_free_space($dirToCheck)/1000000000000),1);
echo ("Analysing: ".$dirToCheck." - <span class='countdown'>".round(($disktotal2/1000),2)." Tb</span> (".$capacity." free)<br /><br /><table style='border:1px solid silver;width:95%'>");

echo ("<tr class='altcolor1'><td width='15%'>&nbsp;DIRECTORY&nbsp;</td><td width='10%'>&nbsp;DISK USED&nbsp;</td><td width='75%'>&nbsp;</td><td>&nbsp;TB&nbsp;</td></tr>");

foreach(glob($dirToCheck.'*', GLOB_ONLYDIR) as $i)						// for each folder found in the 'voduploaddir' root ($i = '/home/sony' )
	{ 
	$capacity=round((disk_free_space($i)/1000000000000),2);
	// echo (" disk_free_space: ".$capacity." | ");	
	//echo "voduploaddir: ".$voduploaddir." ";	
	//echo "<br />".$i." | ";
	$watchfolder=$i."/";
	//echo ("watch folder: ".$watchfolder." | ");													// add trailing slash to folder
	$client = str_replace($voduploaddir,"",$i);
	//echo ($client." ");  
	$filecount = count(glob($watchfolder."*.*"));
	//echo (" | ".$filecount." files | "); 
	//echo glob($watchfolder."*.*")."<br />";

$letslookat='du '.$watchfolder;
$wooh = exec($letslookat);
// $newWooh = str_replace($wooh,"",$watchfolder);
$testX = str_replace($watchfolder,"",$wooh);

			$valX = round($testX/1000,2);
			//echo $valX;
			$billUnits = round($valX/1000000,2);
			if ($valX<1000000000) {$units='Tb';$readableX=round($valX/1000000,2);}
			if ($valX<1000000) {$units='Gb';$readableX=round($valX/1000,1);}
			if ($valX<1000) {$units='Mb';$readableX=round($valX/1,1);}
			$dirPercent=round($testX/($disktotal-$capacity)*100000,3);
			//echo ("<br />".$dirPercent."% ");

echo ("<tr class='altcolor2'><td width='15%'><b>".$watchfolder." </td><td width='10%'>&nbsp; <span class='countdown'>".$readableX."</span> ".$units."</b> (".$dirPercent."%)</td><td width='75%'><img class='dial' src='/jonahv1/images/ltab2.png' /><img class='dial' src='/jonahv1/images/midtab2.png' height='30' width='".$dirPercent."%'/><img class='dial' src='/jonahv1/images/rtab2.png' /></td><td>&nbsp;".$billUnits."&nbsp;</td></tr>");

	foreach(glob($watchfolder.'*', GLOB_ONLYDIR) as $i2)						// for each folder found in the 'voduploaddir' root ($i = '/home/sony' )
		{
			$letslookat='du '.$i2;
			$wooh = exec($letslookat);
			//echo $wooh." | ";
			//echo $watchfolder." | ";
			//echo $i2." <br /> ";
			
			$testX = str_replace($i2,"",$wooh);
			$valX = round($testX/1000,2);
			$billUnits = round($valX/1000000,2);
			//echo $valX;
			if ($valX<1000000000) {$units='Tb';$readableX=round($valX/1000000,2);}
			if ($valX<1000000) {$units='Gb';$readableX=round($valX/1000,1);}
			if ($valX<1000) {$units='Mb';$readableX=round($valX/1,1);}
			$dirPercent=round($testX/($disktotal-$capacity)*100000,3);
			$testDir = str_replace($watchfolder,"",$i2);
			//echo "testX: ".$testX."<br />";
			
			//$newWooh = str_replace($watchfolder,"",$i2);
			echo ("<tr><td><img src='/jonahv1/images/spacer.gif' height='1' width='100' />".$testDir." </td><td>&nbsp; <span class='countdown'>".$readableX."</span> ".$units." (".$dirPercent."%)</td><td><img class='dial' src='/jonahv1/images/ltab.png' /><img class='dial' src='/jonahv1/images/midtab.png' height='30' width='".$dirPercent."%'/><img class='dial' src='/jonahv1/images/rtab.png' /></td><td>&nbsp;".$billUnits."&nbsp;</td></tr>");
			//echo "&nbsp; &nbsp; &nbsp; ".$wooh."<br />";
		}
	}	
echo ("</table><br /><br /><div align='center'>");
include ('/var/www/html/jonahv1/inc/adminfooter.php');
echo ("</div>");

?>	
</div>
</body></html>