<?php
$streamURL = $_GET["streamURL"];
$streamURL = str_replace('://','%3A%2F%2F',$streamURL); //
$streamURL = str_replace('/','%2F',$streamURL);         // 
echo ('<object width="400" height="270"><param name="movie" value="http://live1.ot2.tv/osmf/StrobeMediaPlayback.swf"></param><param name="flashvars" value="src='.$streamURL.'&streamType=live&scaleMode=stretch&verbose=true&verbose=true"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="wmode" value="direct"></param><embed src="http://live1.ot2.tv/osmf/StrobeMediaPlayback.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="direct" width="400" height="270" flashvars="src='.$streamURL.'&streamType=live&scaleMode=stretch&verbose=true&verbose=true"></embed></object>');
