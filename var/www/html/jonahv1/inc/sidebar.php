<!-- <img src="/jonahv1/images/jonah.png" /><br /> -->
<div class='title'>UPLOAD CAPACITY - INGEST1</div><br />
<?php	
$diskfree2TB=round($diskfree2/1000,2);
$disktotal2TB=round($disktotal2/1000,2);
	echo ("<img src='/jonahv1/images/".$diskimagetouse2.".png' /><br />");									// use representative image of diskspace
	echo ("<span class='countdown'><b><span class='vip'>".$diskfree2TB." Tb </span></b>FREE</span> / <b>".$disktotal2TB."</b> Tb ");				// show diskfreespace in Gb
	echo ("&nbsp;[ <a href='/jonahv1/admin/disk.php?nas=ingest1'>details</a> ]&nbsp;");
	// echo ("(".$diskpercent2."%)<br />");																//show percentage free
?>
<br />

<br /><div class='title'>STORAGE CAPACITY - NASA G</div><br />
<?php 
$diskfreeTB=round($diskfree/1000,2);
$disktotalTB=round($disktotal/1000,2);	
	echo ("<img src='/jonahv1/images/".$diskimagetouse.".png' /><br />");									// use representative image of diskspace
	echo ("<span class='countdown'><b><span class='vip'>".$diskfreeTB." Tb</span></b> FREE</span> / <b>".$disktotalTB."</b> Tb ");				// show diskfreespace in Gb
	echo ("&nbsp;[ <a href='/jonahv1/admin/disk.php?nas=wing'>details</a> ]&nbsp;");
	// echo ("(".$diskpercent."%)<br />");																//show percentage free
?>

<br /><br />
<?php  echo ("<br />Updated | ".$formattedtime); ?>
<br />Updating in <span id="countdown" class="countdown"><?php  echo($refreshtime); ?></span> secs<br /><br />