<?php
$streamURL = $_GET["streamURL"];
$streamURL = str_replace('://','%3A%2F%2F',$streamURL); //
$streamURL = str_replace('/','%2F',$streamURL);         // 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>Jonah - analysis engine</title>
    <link href="/jonah/css/main.css" type="text/css" rel="stylesheet">
   </head> 
<body bgcolor="black"><div id='osmfPlayer' style='background-color:black; padding:5px;' align="center"><?
echo ('<object width="640" height="360"> <param name="movie" value="http://osmf.org/dev/2.0gm/StrobeMediaPlayback.swf"></param><param name="flashvars" value="src='.$streamURL.'&scaleMode=stretch&optimizeInitialIndex=false&poster=http%3A%2F%2Flive1.ot2.tv%2Fuspgui%2Fimages%2Fposter.jpg"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="wmode" value="direct"></param><embed src="http://osmf.org/dev/2.0gm/StrobeMediaPlayback.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="direct" width="640" height="360" flashvars="src='.$streamURL.'&scaleMode=stretch&optimizeInitialIndex=false&poster=http%3A%2F%2Flive1.ot2.tv%2Fuspgui%2Fimages%2Fposter.jpg"></embed></object>');
?>
</div></body></html>