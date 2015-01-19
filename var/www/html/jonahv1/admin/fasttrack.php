<?php 
ini_set ("display_errors", "1");
error_reporting(E_ALL);
$queueItem 	= $_GET["item"]; 
$cat 		= $_GET["cat"];
// $org 		= $_GET["org"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Jonah - analyse engine</title>
	<link href="/jonahv1/css/main.css" type="text/css" rel="stylesheet">
    <script src="/jonahv1/scripts/libraries.js" type="text/javascript"></script>
    <META HTTP-EQUIV="MSThemeCompatible" Content="Yes">
    <?php  include ('/var/www/html/jonahv1/inc/jquery.htm'); ?>
</head>

<body>
<table cellpadding="0" cellspacing="0" class="jonahtable">
<tr valign="top">	
<td>
<?php  include ('/var/www/html/jonahv1/inc/sidebar2.php'); ?>
</td>
<td>

<!-- // mockup for ftp queue -->
<?php  include ('/var/www/html/jonahv1/inc/fasttrack-process.php'); ?>

<br /><br />

<!-- // -->
</td><!-- end of content cell in main table // -->
</tr>

<tr><td align="left" valign="bottom"><form valign="bottom"><INPUT class="softbutton" onClick="showDebug()" value="showDebug()" type="button" disabled="disabled"></form></td><td align="center">
<?php  include ('/var/www/html/jonahv1/inc/adminfooter.php'); ?>
</td></tr>
</table>

</body>
</html>	