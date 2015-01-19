<html>
	<head>
<script src="http://code.jquery.com/jquery-latest.js"></script>

<script>
//refresh the image every 60seconds
var xyro_refresh_timer = setInterval(xyro_refresh_function, 60000);

function xyro_refresh_function(){
//refreshes an image with a .xyro_refresh class regardless of caching
    //get the src attribute
    source = jQuery(".xyro_refresh").attr("src");
    //remove previously added timestamps
    source = source.split("?", 1);//turns "image.jpg?timestamp=1234" into "image.jpg" avoiding infinitely adding new timestamps
    //prep new src attribute by adding a timestamp
    new_source = source + "?timestamp="  + new Date().getTime();
    //alert(new_source); //you may want to alert that during developement to see if you're getting what you wanted
    //set the new src attribute
    jQuery(".xyro_refresh").attr("src", new_source);
}
</script>
	
</head>
<body>
	
	
	
	
	
    <img src = '/thumbs/aa-port1-monitor.jpg' id='xyro_refresh' style='padding:2px;' />
    <img src = '/thumbs/aa-port2-monitor.jpg' id='aa-port2-monitor' style='padding:2px;' />
    <img src = '/thumbs/aa-port3-monitor.jpg' id='aa-port3-monitor' style='padding:2px;' />
    <img src = '/thumbs/aa-port5-monitor.jpg' id='aa-port5-monitor' style='padding:2px;' />
    <img src = '/thumbs/aa-port6-monitor.jpg' id='aa-port6-monitor' style='padding:2px;' />
    <img src = '/thumbs/aa-port7-monitor.jpg' id='aa-port7-monitor' style='padding:2px;' />
</body>
</html>