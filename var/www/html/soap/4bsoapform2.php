<?php    
ini_set ("display_errors", "1");
error_reporting(E_ALL);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>Jonah - soap test</title>
    <link href="/jonahv1/css/main.css" type="text/css" rel="stylesheet">
   </head>
<body>

<div id='' style='padding:5px;'><br />
<div align='center' style='align:center;'>
    <table>
    <form name='firingonallcylinders' class='adminini' action="/soap/4bsoapcall.php" method="POST">

    <tr><td>Job Name&nbsp;</td><td>&nbsp;</td><td><input type='text' id="jobname" name='jobname' value='delstest1' /></td></tr> 
    <tr><td>Preset&nbsp;</td><td>&nbsp;</td><td><input type='text' name='presetid' value='muse4b' /></td></tr>    
    <tr><td>InputFilename&nbsp;</td><td>&nbsp;</td><td><input type='text' name='inputfilename' value='file://192.168.0.6/secure/' /></td></tr>
    
    <tr><td colspan='3'>e.g. <i>file://192.168.0.6/secure/delstest/firework.mp4</i></td></tr>
    
    <!-- file://192.168.0.6/secure/delstest/firework.mp4 -->
        
    <tr><td>OutputFilename</td><td>&nbsp;</td><td><input type='text' name='outputfilename' value='file://nasa/wing/vod/' /></td></tr>
    
    <tr><td colspan='3'>e.g. <i>file://nasa/wing/vod/delstest/deltest1234</i></td></tr>
        
    <tr><td>IP&nbsp;</td><td>&nbsp;</td><td><input type='hidden' name='ip' value='<? echo ($_SERVER['REMOTE_ADDR']);?>' /><? echo ($_SERVER['REMOTE_ADDR']);?></td></tr>   
    <tr><td></td><td>&nbsp;</td><td><input value="SUBMIT TO SOAP API" type="submit" action="/soap/4bsoapcall.php" /></td></tr>        
    </form>
    </table>    

</div>
</div>

<div class='disclaimer' style='text-align:center;'>Del | USPGUI&trade; Engine 1.0 &nbsp;&nbsp;</div></div>

 
 
<p><a id='fred' href="#" onclick="addTxt('this is some text!', 'info')">Click here to add Text</a></p>
<form name="test">
<input type="text" id="info" value="">
</form>

</body></html>