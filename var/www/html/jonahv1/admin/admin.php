<?php 
	 ini_set ("display_errors", "1");
	 error_reporting(E_ALL);
	 $formattedtime=date("F j, Y, g:i a");
	 $userip=$_SERVER['REMOTE_ADDR'];
	 
	 $adminVar=file_get_contents('/var/www/html/jonahv1/admin/config.json'); 	//read admin variable from local json file
	 $config = json_decode($adminVar, true);							// json decode array
	 
	$voddrive = $config['voddrive'];								// set disk array used for vod storage
	$voduploaddir = $config['voduploaddir'];						// set disk path used for vod upload
	$pathtoinspect = $config['pathtoinspect'];						// ffmpeg inspection text files go here (temp folder
	$pathtoqueue = $config['pathtoqueue'];							// place files to be transcoded here
	$pathtoxml = $config['pathtoxml'];								// place associated xml here
	$outputpath = $config['outputpath'];							// place output path (ie fms vod folder) here	
	$presetpath = $config['presetpath'];							// place associated xml here
	$allowed_video_types = $config['allowedvideotypes'];			// set allowed video types to process
	$allowed_xml_types = $config['allowedxmltypes'];				// set allowed xml suffixes
	$minstowait = $config['minstowait'];							// how long to wait for xml before queuing for transcode
	$lastupdatedby = $config['9'];									// read IP address of last person to modify admin config file
	$orphanpath = $config['orphanpath'];							// output path for non xml, non video files
	$logpath = $config['logpath'];									// path to log files
	$parallel = $config['parallel'];								// how many parallel transcodes
	$passes = $config['passes'];									// force 1 or allow 2 pass
	$manifestpath = $config['manifestpath'];						// path to manifest file output
	$thumbpath = $config['thumbpath'];								// thumbnail output path
	$vidmanapi = $config['vidmanapi'];								// url to video manager api (without http://)
	$identmanapi = $config['identmanapi'];							// url to acl api (without http://)
	$streammanapi = $config['streammanapi'];						// url to stream manager api (without http://)
	$watermarkon = $config['watermarkon'];
	$watermarkurl = $config['watermarkurl'];						// url to watermark image (without http://)
	$watermarktxt = $config['watermarktxt'];
	$quarantine = $config['quarantine'];						
	$alertemail = $config['alertemail'];
	$autofix = $config['autofix'];
		 
	$diskfree=round((disk_free_space($voddrive)/1000000000),1);		// get disk-unused in Gbytes
	$disktotal=round((disk_total_space($voddrive)/1000000000),1);	// get disk-size in Gbytes
	$diskpercent = round(($diskfree/$disktotal*100));
	$diskpercentused=100-$diskpercent;
	$diskimagetouse=intval($diskpercentused/10)*10;					// use closest disk image to capacity used (ie under 10%, 10-19%, 20-29% etc)	 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Jonah - TVPlayer ingest monitor</title>
	<link href="/jonahv1/css/main.css" type="text/css" rel="stylesheet">
	<META HTTP-EQUIV="MSThemeCompatible" Content="Yes">
	<script type="text/javascript" src="/jonahv1/scripts/libraries.js"></script>
    <?php  include ('/var/www/html/jonahv1/inc/jquery.htm'); ?>
</head>
<body><?php  include ('/var/www/html/jonahv1/inc/topbar.php'); ?>
<table cellpadding="0" cellspacing="0" class="jonahtable" width="95%">
<tr valign="top">	
<td>

<?php  include ('/var/www/html/jonahv1/inc/sidebar2.php'); ?>

</td>
<td>
<?php
//file_put_contents('/var/www/html/jonahv1/admin/admin.json', $filestobestored);
?>

<span class='countdown'>Jonah Administrative Page</span>
<br /><br />
<!-- outer container hidden-->

<form class='adminini' action="/jonahv1/admin/update.php" method="POST" name="configForm">
<table class='jonahtable'>
<tr class='tabhead-admin'><td>DESCRIPTION </td><td>&nbsp; </td><td> VALUE </td><td>&nbsp; </td><td> [debug] variable name </td><td> LOCKED </td></tr>

<tr><td colspan='6'><span class='countdown bordery'>-- General --</span></td></tr>

<tr><td>global debug</td><td>&nbsp; </td><td><input name="globalDebug" class='adinput' type='text'  value='disabled' /></td><td>&nbsp; </td><td> $globalDebug</td><td><img src='/jonahv1/images/locked.png' onClick='showVod();this.src="/jonahv1/images/unlocked.png"' /></td></tr>

<tr><td>root path to disk array used for vod storage </td><td>&nbsp; </td><td><input name="voddrive" class='adinput' type='text'  value='<?php  echo ($voddrive);?>' /></td><td>&nbsp; </td><td> $voddrive</td><td><a href='/jonahv1/inc/validate-path.php?analysis=<?php  echo $voddrive; ?>' rel="shadowbox[Mixed];width=400;height=400" />validate path</a></td></tr>
<tr><td>root path to vod upload parent directory </td><td>&nbsp; </td><td><input name="voduploaddir" class='adinput' type='text'  value='<?php  echo ($voduploaddir);?>' /></td><td>&nbsp; </td><td> $voduploaddir</td><td><a href='/jonahv1/inc/validate-path.php?analysis=<?php  echo $voduploaddir; ?>' rel="shadowbox[Mixed];width=400;height=400" />validate path</a></td></tr>
<tr><td>root path to ffmpeg debug files </td><td>&nbsp; </td><td><input name="pathtoinspect" class='adinput' type='text'  value='<?php  echo ($pathtoinspect);?>' /></td><td>&nbsp; </td><td> $pathtoinspect</td><td><img src='/jonahv1/images/locked.png' onClick='showInspect();this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>root path to transcode queue folder </td><td>&nbsp; </td><td><input name="pathtoqueue" class='adinput' type='text'  value='<?php  echo ($pathtoqueue);?>' /></td><td>&nbsp; </td><td> $pathtoqueue</td><td><img src='/jonahv1/images/locked.png' onClick='showQueue();this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>root path to associated xml </td><td>&nbsp; </td><td><input name="pathtoxml" type='text'  class='adinput' value='<?php  echo ($pathtoxml);?>' /></td><td>&nbsp; </td><td> $pathtoxml</td><td><img src='/jonahv1/images/locked.png' onClick='showXml();this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>root path to orphans </td><td>&nbsp; </td><td><input name="orphanpath" type='text' class='adinput' value='<?php  echo ($orphanpath);?>' /></td><td>&nbsp; </td><td> $orphanpath</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>root path to transcode log </td><td>&nbsp; </td><td><input name="logpath" type='text' class='adinput' value='<?php  echo ($logpath);?>' /></td><td>&nbsp; </td><td> $logpath</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>root path to vod output </td><td>&nbsp; </td><td><input name="outputpath" type='text' class='adinput' value='<?php  echo ($outputpath);?>' /></td><td>&nbsp; </td><td> $outputpath</td><td><a href='/jonahv1/inc/validate-path.php?analysis=<?php  echo $outputpath; ?>' rel="shadowbox[Mixed];width=400;height=400" />validate path</a></td></tr>
<tr><td>root path to presets </td><td>&nbsp; </td><td><input name="presetpath" type='text' class='adinput' value='<?php  echo ($presetpath);?>' /></td><td>&nbsp; </td><td> $presetpath</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>output path for design thumbnails</td><td>&nbsp; </td><td><input name="thumbpath" type='text' class='adinput' value='<?php  echo ($thumbpath);?>' /></td><td>&nbsp; </td><td> $thumbpath</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>output path for manifest files </td><td>&nbsp; </td><td><input name="manifestpath" type='text' class='adinput' value='<?php  echo ($manifestpath);?>' /></td><td>&nbsp; </td><td> $manifestpath</td><td><a href='/jonahv1/inc/validate-path.php?analysis=<?php  echo $manifestpath; ?>' rel="shadowbox[Mixed];width=400;height=400" />validate path</a></td></tr>

<tr><td colspan='6'><span class='countdown bordery'>-- Config --</span></td></tr>

<tr><td>allowed video types by suffix</td><td>&nbsp; </td><td><input name="allowedvideotypes" type='text'  class='adinput' value='<?php  echo ($allowed_video_types);?>' /></td><td>&nbsp; </td><td> $allowed_video_types</td><td><img src='/jonahv1/images/locked.png' onClick='showVideotypes();this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>allowed xml type by suffix </td><td>&nbsp; </td><td><input name="allowedxmltypes" type='text'  class='adinput' value='<?php  echo ($allowed_xml_types);?>' /></td><td>&nbsp; </td><td> $allowed_xml_types</td><td><img src='/jonahv1/images/locked.png' onClick='showXmlTypes();this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>minutes to wait for associated xml </td><td>&nbsp; </td><td><input name="minstowait" type='text'  class='adinput' value='<?php  echo ($minstowait);?>' /> <input name="var9" type='hidden' value="<?php  echo ($userip); ?>" /></td><td>&nbsp; </td><td> $minstowait</td><td><img src='/jonahv1/images/locked.png' onClick='showMins();this.src="/jonahv1/images/unlocked.png"' /></tr>

<tr><td colspan='6'><span class='countdown bordery'>-- Transcode --</span></td></tr>

<tr><td>no. of parallel transcodes </td><td>&nbsp; </td><td><input name="parallel" type='text' class='adinput' value='<?php  echo ($parallel);?>' /></td><td>&nbsp; </td><td> $parallel</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>no. of transcode passes </td><td>&nbsp; </td><td><input name="passes" type='text' class='adinput' value='<?php  echo ($passes);?>' /></td><td>&nbsp; </td><td> $passes</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>

<tr><td colspan='6'><span class='countdown bordery'>-- Watermark --</span></td></tr>

<tr><td>Watermark on/off</td><td>&nbsp;<input name="watermarkon" type='checkbox' value='Yes' <?php  echo($watermarkon); ?> />&nbsp; </td><td><input name="watermarkurl" type='text' class='adinput' value='<?php  echo ($watermarkurl);?>' /></td><td>&nbsp; </td><td>$watermarkurl</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>Watermark text </td><td>&nbsp; </td><td><input name="watermarktxt" type='text' class='adinput' value='<?php  echo ($watermarktxt);?>' /></td><td>&nbsp; </td><td> $watermarktxt</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>


<tr><td colspan='6'><span class='countdown bordery'>-- API --</span></td></tr>
<tr><td>video manager API url </td><td>&nbsp; </td><td><input name="vidmanapi" type='text' class='adinput' value='<?php  echo ($vidmanapi);?>' /></td><td>&nbsp; </td><td> $vidmanapi</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>identification API url </td><td>&nbsp; </td><td><input name="identmanapi" type='text' class='adinput' value='<?php  echo ($identmanapi);?>' /></td><td>&nbsp; </td><td> $identmanapi</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>stream manager API url </td><td>&nbsp; </td><td><input name="streammanapi" type='text' class='adinput' value='<?php  echo ($streammanapi);?>' /></td><td>&nbsp; </td><td> $streammanapi</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>

<tr><td colspan='6'><span class='countdown bordery'>-- Quarantine --</span></td></tr>
<tr><td>quarantine path </td><td>&nbsp; </td><td><input name="quarantine" type='text' class='adinput' value='<?php  echo ($quarantine);?>' /></td><td>&nbsp; </td><td> $quarantine</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>alert email </td><td>&nbsp; </td><td><input name="alertemail" type='text' class='adinput' value='<?php  echo ($alertemail);?>' /></td><td>&nbsp; </td><td> $alertemail</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>
<tr><td>attempt to autofix </td><td>&nbsp;<input name="autofix" type='checkbox' value='Yes' <?php  echo($autofix); ?> />&nbsp; </td><td>&nbsp;</td><td>&nbsp; </td><td> $autofix</td><td><img src='/jonahv1/images/locked.png' onClick='this.src="/jonahv1/images/unlocked.png"' /></td></tr>

<tr><td colspan='6'><br /><br />Last updated by: <?php  echo ($config['9']); ?></td></tr>
<tr><td>&nbsp; </td><td>&nbsp; </td><td align="right">check changes before submitting >> </td><td>&nbsp; </td><td> <INPUT class="softbutton" 
onClick="showVod();showUpload();showInspect();showQueue();showXml();showVideotypes();showXmlTypes();showMins();document.forms["configForm"].submit();" value="submit changes" type="submit" action="/jonahv1/admin/update.php" /></td><td> </td></tr></table>

</form>

<!-- end of content cell in main table // -->
</td></tr>

<tr><td></td><td align="center">
<a href='#'>Ingest Audit</a> | <a href='#'>Logview</a> | <a href='#'>Manage</a>
</td></tr>

<tr><td></td><td align="center">
<?php  include ('/var/www/html/jonahv1/inc/adminfooter.php'); ?>
</td></tr>
</table>

</body>
</html>	
