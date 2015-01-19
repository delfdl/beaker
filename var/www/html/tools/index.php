<?  
ini_set ("display_errors", "1");
error_reporting(E_ALL);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title>Jonah - toolkit</title>
    <link href="/jonahv1/css/main.css"                                  rel="stylesheet" type="text/css" />
    <link href="/jonahv1/css/ui-lightness/jquery-ui-1.8.21.custom.css"  rel="stylesheet" type="text/css" />
    <link href="/jonahv1/js/jquery.alerts.css"                          rel="stylesheet" type="text/css" media="screen" />
    <link href="/jonahv1/shadowbox/shadowbox.css"                       rel="stylesheet" type="text/css" />
 	<link rel="stylesheet" href="/stylesheets/jquery-ui.css" />    
    
	<script src="/jonahv1/js/jquery-1.7.2.min.js"               type="text/javascript"></script>
    <script src="/jonahv1/js/jquery-ui-1.8.21.custom.min.js"    type="text/javascript"></script>
    <script src="/jonahv1/js/jquery.alerts.js"                  type="text/javascript"></script>
    <script src="/jonahv1/shadowbox/shadowbox.js"               type="text/javascript"></script>
    <script src="/jonahv1/js/tooltip-jquery.js"                 type="text/javascript"></script>
    <script src="/jonahv1/js/tooltip-main.js"                   type="text/javascript"></script>
  	<script src="/javascripts/jquery-1.9.1.js"></script>
  	<script src="/javascripts/jquery-ui.js"></script>
    
	<script type="text/javascript">
    Shadowbox.init({
    });
    </script>
 
  <style>
    .toggler { width: 500px; height: 100px; position: relative; }
    #homeButton { padding: 5 10; text-decoration: none; }
    #button1 { padding: 5 10; text-decoration: none; }
	#button2 { padding: 5 10; text-decoration: none; }
	#button3 { padding: 5 10; text-decoration: none; }
	#button4 { padding: 5 10; text-decoration: none; }
    #button5 { padding: 5 10; text-decoration: none; }
    #button6 { padding: 5 10; text-decoration: none; }
    #effect { width: 1024px; height: 800px; margin-left:15px; padding: 10px; position: relative; background: #fff; border:1px solid silver;}
    #effect h2 { margin: 0; padding: 0.4em; text-align: center; }
  </style>

  <script>
  $(function() {

    $( "#button1" ).click(function() {
    $( "#effect" ).animate({
          backgroundColor: "#f0f0f0",
          color: "#000",
          width: 1580,
		  height: 640,		  
        }, 1000 );   });
   
       $( "#button2" ).click(function() {
    $( "#effect" ).animate({
          backgroundColor: "#f0f0f0",
          color: "#000",
          width: 1600,
		  height: 400,		  
        }, 1000 );   });
   
     $( "#button3" ).click(function() {
    $( "#effect" ).animate({
          backgroundColor: "#f1f1f1",
          color: "#000",
          width: 1000,
		  height: 640,		  
        }, 1000 );   });
		
	$( "#button4" ).click(function() {
    $( "#effect" ).animate({
          backgroundColor: "#f0f0f0",
          color: "#000",
          width: 1280,
		  height: 800,		  
        }, 1000 );   });
        
        $( "#button5" ).click(function() {
    $( "#effect" ).animate({
          backgroundColor: "#f0f0f0",
          color: "#000",
          width: 1800,
          height: 500,          
        }, 1000 );   }); 
        
        $( "#button6" ).click(function() {
    $( "#effect" ).animate({
          backgroundColor: "#f0f0f0",
          color: "#000",
          width: 1024,
          height: 600,          
        }, 1000 );   });           

  });

function filterDB(str)
 {
 if (str=="")
   {
   document.getElementById("innertxtHint").innerHTML="";
   return;
   } 
 if (window.XMLHttpRequest)
   {// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
   }
 else
   {// code for IE6, IE5
   xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
   }
 xmlhttp.onreadystatechange=function()
   {
   if (xmlhttp.readyState==4 && xmlhttp.status==200)
     {
     document.getElementById("innertxtHint").innerHTML=xmlhttp.responseText;
     }
   }
 xmlhttp.open("GET","/tools/filter.php?q="+str,true);
 xmlhttp.send();
 }
 
function changeURL(newPath)
 {
 if (window.XMLHttpRequest)
   {// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
   }
 else
   {// code for IE6, IE5
   xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
   }
 xmlhttp.onreadystatechange=function()
   {
   if (xmlhttp.readyState==4 && xmlhttp.status==200)
     {
     document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
     }
   }
 xmlhttp.open("GET",newPath,true);
 xmlhttp.send();
 }

function loadffmpeg(newhash)
 {
 if (window.XMLHttpRequest)
   {// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
   }
 else
   {// code for IE6, IE5
   xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
   }
 xmlhttp.onreadystatechange=function()
   {
   if (xmlhttp.readyState==4 && xmlhttp.status==200)
     {
     document.getElementById("qcffmpeg").innerHTML=xmlhttp.responseText;
     }
   }
 xmlhttp.open("GET","/tools/returnffmpeg.php?newhash="+newhash,true);
 xmlhttp.send();
 }
 
 function submitForm()
 {
if (window.XMLHttpRequest)
   {// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
   }
 else
   {// code for IE6, IE5
   xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
   }
 xmlhttp.onreadystatechange=function()
   {
   if (xmlhttp.readyState==4 && xmlhttp.status==200)
     {
     document.getElementById("innertxtHint").innerHTML=xmlhttp.responseText;
     }
   }
 xmlhttp.open("POST","/tools/associate2.php",true);
 // document.getElementById("srtAssociation").submit();
 xmlhttp.send();
 }
 </script>
 
<? include ('/var/www/jonahv1/inc/jquery.htm'); ?>
</head>
<body><? include ('/var/www/jonahv1/inc/topbar.php'); ?><br />&nbsp;
 <a href="/tools/index.php" id="homebutton" class="ui-state-default ui-corner-all">Home</a>
 <a href="#" id="button2" class="ui-state-default ui-corner-all" onClick="changeURL('/tools/queue.php')">Queue via API</a>
 <a href="#" id="button3" class="ui-state-default ui-corner-all" onClick="changeURL('/tools/manual.php')">Manual tools</a>
 <a href="#" id="button1" class="ui-state-default ui-corner-all" onClick="changeURL('/tools/subs.php')">Subs</a>
 <a href="#" id="button6" class="ui-state-default ui-corner-all" onClick="changeURL('/api/check.php')">Jonah&trade; Logs</a>
 <a href="#" id="button4" class="ui-state-default ui-corner-all" onClick="changeURL('/tools/disk.php?nas=ingest1')">Disk management</a>
 <a href="#" id="button5" class="ui-state-default ui-corner-all" onClick="changeURL('/tools/profileeditor.php')">Profile Editor</a> &nbsp;
 <a href="http://live1.ot2.tv/uspgui/" id="button5" class="ui-state-default ui-corner-all">Live Monitoring</a>
<div class="toggler">
	<div id="effect" class="ui-widget-content ui-corner-all">
    <div id="txtHint">
<br />
		<div id="innertxtHint" style='padding:10px;border:1px solid silver;' class='ui-corner-all shadow'>

<?php




?>       </div>
		</div>
	</div>
</div>

</body>
</html> 