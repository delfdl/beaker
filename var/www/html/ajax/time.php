<?php
 $rndtime = microtime(true);
 $trueTime1 = date("d M y, H");
 $trueTime2 = date("i");
 $zooTime = intval(date("H"))+4; 
 echo ("Updated at &nbsp;".$trueTime1.":".$trueTime2."&nbsp; | &nbsp; &nbsp;Zoo Local Time = ".$zooTime.":".$trueTime2);
	
?>