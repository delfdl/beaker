<?php
	$currenttime=date("U");												// get timestamp
	$result = $_REQUEST["table-3"];
    echo "received new order: ";
    array_shift($result);
    foreach ($result as $value)
    {
        echo ($value.",");
    }
    echo "<br /><br />";
    echo " new order: ".$result;
	$adminVar=file_get_contents('/var/www/jonahv1/admin/config.json'); 	// read admin variable from local json file (use mySql db later)
	$config = json_decode($adminVar, true);								// json decode array
	
	$fullQueue=file_get_contents('/var/www/jonahv1/queue/queue.json'); 	// 	replace by mySql record update (add item to bottom of queue)
	$queueAgefull=date("U",filemtime('/var/www/jonahv1/queue/queue.json'));
	$queueAge=intval((($currenttime-$queueAgefull)/60));					// how many full minutes difference between video uploaded and current time (e.g '2')
	
	if ($queueAge<1) 													// for safety, dont allow queue-ordering if queue has been updated less than 60 secs ago
	{
		echo ("Failed to update - queue recently updated<br />");
		// break;
	}

	$explodeID="*";														// 	used to separate json-encoded arrays
	$explodeIDcount=substr_count($fullQueue,$explodeID);				//  count number of explodeIDs in $fullQueue
	
	$queueExplode=explode($explodeID,$fullQueue);						// explode queue.json by '*' into array
	$i=0;
	$newQueue="";
	
	// re-order lines according to $result
	
	foreach($result as $value) 
	{

		if (!$queueExplode[$value])
		{}
		else 
		{
			$i++;
			// echo ("<b>$i</b> ".$queueExplode[$value]."<br />");		// debug
			$tmp = $queueExplode[$value];
			$jQueue=json_decode($tmp,true);			
			$jQueue['queue-id']=$i;										// changes queue-id
			$correctedQueueItem = json_encode($jQueue,true);
			$newQueue=$newQueue."*".$correctedQueueItem;
		}
	//$i=$value;
	}
	
	
	echo ("<b>Saving</b>: ".$newQueue);
	if (!copy('/var/www/jonahv1/queue/queue.json','/var/www/jonahv1/queue/queue.bak')); 
	{
  echo ('failed to backup queue.json<br />');
	rename('/var/www/jonahv1/queue/queue.json','/var/www/jonahv1/queue/queue.bak');
	}
	 		// make backup
	file_put_contents('/var/www/jonahv1/queue/queue.json', $newQueue); 
	echo ("Queue re-ordered<br /><a href='javascript:self.location.reload(true);'>click to reload</a>");
	
	echo ("<script>");
	// echo ("self.location.reload(true)");
	echo ("</script>");
?>