<?php
         ini_set ("display_errors", "1");
         error_reporting(E_ALL);             
         if (isset($_GET["url"])) 
         {
           $url = $_GET["url"]; 
         } 
        else {$url = 'http://live.ot2.tv/live/skynews/skynews.isml/skynews.f4m';} 
        
         
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title>Shango</title>
	<meta name="description" content="" />
    
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
    #homeButton { padding: 5 10; text-decoration: none; }
    #button1 { padding: 5 10; text-decoration: none; }
    #button2 { padding: 5 10; text-decoration: none; }
    #button3 { padding: 5 10; text-decoration: none; }
    #button4 { padding: 5 10; text-decoration: none; }
    #button5 { padding: 5 10; text-decoration: none; }
    #effect { width: 720px; height: 400px; margin-left:5px; padding: 5px; position: relative; background: #fff; border:1px solid silver;}
    .shangoBox {width:640px; height:360px; display:inline-block; padding:0;}
    </style>
	
	<script src="js/swfobject.js"></script>
	<script>
	 	var anticache = Math.floor(Math.random()*10000);
		var flashvars = {
			//Set player 'mode'
			//
			
			isCustomerPortal:false,
			
			//For Zuul
			//
			
			accessToken:"969c1cc72b777ace0c6c78a9b43f351b2c2ea744",
			categoryId:18,
			videoId:8315,
			epgChannelId:NaN,
			streamType:"vod",
			
			//For CMS Preview
			//
			
			videoUrl:"<? echo ($url); ?>", 
			drmToken:"",
			
			//General player parameters
			//
			
			autoplay:true,
			hdMinThreshold:576,
			fourkMinThreshold:1081,
			forceReBufferOnBitrateSwitch:false,
			volume:.75,
            logoUrl:"img/logos/ottilus-bug.png",
            posterUrl:"https://vod-images.s3.amazonaws.com/vod/14923/testcard_hd_169_640x480.jpg",
            skinUrl:"img/skins/standard",
            captionUrls:"[{\"url\":\"http:\/\/vod1.ot2.tv\/vod\/otv\/mtv\/16287\/subs_en.dfxp\", \"label\":\"English\"}, {\"url\":\"http:\/\/vod1.ot2.tv\/vod\/otv\/mtv\/16287\/subs_nl.dfxp\", \"label\":\"Dutch\"}]",
            locale:"en_US"
		};
		var params = {
			menu: "false",
			scale: "noScale",
			allowFullscreen: "true",
			allowScriptAccess: "always",
			bgcolor: "0",
			wmode: "direct" // can cause issues with FP settings & webcam
		};
      
       
		var attributes = {
			id:"shangoPlayer"
		};
		swfobject.embedSWF(
			"OttilusPlayer.swf?" + anticache, 
			"shangoPlayer", "100%", "100%", "10.0.0", 
			"expressInstall.swf", 
			flashvars, params, attributes);

   $(function() {

    $( "#button1" ).click(function() {
    $( "#shangoPlayer" ).animate({
          backgroundColor: "#f0f0f0",
          color: "#000",
          width: 320,
          height: 180,          
        }, 1000 );   });
   
       $( "#button2" ).click(function() {
    $( "#shangoPlayer" ).animate({
          backgroundColor: "#f0f0f0",
          color: "#000",
          width: 480,
          height: 270,          
        }, 1000 );   });
   
     $( "#button3" ).click(function() {
    $( "#shangoPlayer" ).animate({
          backgroundColor: "#f1f1f1",
          color: "#000",
          width: 640,
          height: 360,          
        }, 1000 );   });
        
    $( "#button4" ).click(function() {
    $( "#shangoPlayer" ).animate({
          backgroundColor: "#f0f0f0",
          color: "#000",
          width: 960,
          height: 540,          
        }, 1000 );   });
        
        $( "#button5" ).click(function() {
    $( "#shangoPlayer" ).animate({
          backgroundColor: "#f0f0f0",
          color: "#000",
          width: 1280,
          height: 720,          
        }, 1000 );   }); 
        
    $( "#button6" ).click(function() {
    $( "#shangoPlayer" ).animate({
          backgroundColor: "#f0f0f0",
          color: "#000",
          width: 1920,
          height: 1080,          
        }, 1000 );   }); 
        
    $( "#button7" ).click(function() {
    $( "#shangoPlayer" ).animate({
          backgroundColor: "#f0f0f0",
          color: "#000",
          width: 3840,
          height: 2160,          
        }, 1000 );   });           

  });
    </script>
</head>
<body><? include ('/var/www/jonahv1/inc/topbar.php'); ?>

<!-- <div class="toggler">  -->
 
	<div class='shangoBox' style='padding:5px;'>    
    <div class='shangoBox ui-corner-all' style='height:30px; border:1px solid silver; padding:5px;'>&nbsp;&nbsp;
     <a href="#" title='320x240' id="button1" class="ui-state-default ui-corner-all">320x180 - 3G</a>&nbsp;
     <a href="#" title='480x270' id="button2" class="ui-state-default ui-corner-all">480x270 - S</a>&nbsp;
     <a href="#" title='640x360' id="button3" class="ui-state-default ui-corner-all">640x360 - M</a>&nbsp;
     <a href="#" title='950x540' id="button4" class="ui-state-default ui-corner-all">950x540 - L</a>&nbsp;
     <a href="#" title='1280x720' id="button5" class="ui-state-default ui-corner-all">1280x720 - HD 720</a>&nbsp;
     <a href="#" title='1920x1080' id="button6" class="ui-state-default ui-corner-all">1920x1080 - HD Full</a>&nbsp;
     <a href="#" title='3840x2160' id="button7" class="ui-state-default ui-corner-all">3840x2160 - 4K</a>&nbsp;
     </div><br clear='all' />    
    <div id="shangoPlayer">
		<h1>Shango</h1>
		<p><a href="http://www.adobe.com/go/getflashplayer">Get Adobe Flash player</a> If you have it, please wait to initialise...</p>
	</div>
    </div><br clear='all' />
    <div class='shangoBox ui-corner-all' style='height:20px; border:1px solid silver; padding:5px;'><? echo ($url); ?></div>
    
<!-- </div> -->

</body>
</html>