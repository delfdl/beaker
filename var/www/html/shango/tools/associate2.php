<?php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $srt = $_REQUEST["srt"];
     $assoc = $_REQUEST["assoc"];
     $charset = $_REQUEST["charset"];
     $bom = $_REQUEST["bom"];
     
     $correction = 0;
     if ($charset=='UTF-8' && $bom=='none') {$correction=1;}  // recreate UTF-8 file with Byte Order Marker
     
     echo "debug mode ON - ";
     echo "srt: ".$srt."<br />";
     echo 'assoc: '.$assoc.'<br />';

 //  $target = $_REQUEST["assoc"]; 
 //  $path = str_replace(basename($target),"",$target);        // target directory
 //  $hash = substr($path,strrpos($path,'/')+1 );
 //  echo $hash;
     
     $file = $srt;
     $srt = str_replace('-','_',$srt);
     $srt = str_replace(' ','_',$srt);
     
     //$newfile = '/mount/wing/vod/otv/'.$hash.'/'.$hash.'.srt';
     
     if (strpos($srt,'_')) 
     {
         $lang1 = strrchr($srt,'_');
         echo 'lang1: '.$lang1.'<br />';
         $lang2 = strstr($lang1,'.',true);
         echo 'lang2: '.$lang2.'<br />';
         $lang3 = str_replace('_','',$lang2);
         echo 'lang3: '.$lang3.'<br />';
         $lang = str_replace('.','',$lang3);
         echo 'lang: '.$lang.'<br />';
     }
     else {$lang = 'und';}

     $newfile = $assoc.'subs_'.$lang.'.srt';

     if ($correction=0)
     {  echo ("About to copy <b>".$file."</b> to <b>".$newfile."</b><br /><br />");
        $bomFix = 'gvim -c "set bomb|wq" '.$file;
        exec ($bomFix);
        if (!file_exists($file)) {echo ('wait... not sure source file exists!');}
        if (!copy($file, $newfile)) 
             {
             echo ("failed to copy ".$file."<br /><br />");
             }
     }
     else
     {
        $bomFix = 'gvim -c "set bomb|wq" '.$file;
        exec ($bomFix);
        echo ("About to recreate <b>".$file."</b> as <b>".$newfile."</b> with UTF-8/BOM<br /><br />");
        $info = file_get_contents($file);        // read original subs
        $outputfile = @fopen($newfile, "w");    // open output file
        $bomContent = b"\xEF\xBB\xBF".$info;     // add Byte Order Marker to front of string
        fwrite($outputfile, $bomContent);       //
        fclose($outputfile);
        $bomFix = 'gvim -c "set bomb|wq" '.$outputfile;
        exec ($bomFix);
     }
       
    $assoc = urlencode($assoc);
     
    $url = 'http://live.ot2.tv/tools/api/associateremote.php?assoc='.$assoc; 
    $ch = curl_init(); 
    echo ("initiating ".$url."<br />");
    $timeout = 5; 
    curl_setopt($ch,CURLOPT_URL,$url); 
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
    $response = curl_exec($ch); 
    curl_close($ch); 
 
echo($response);   
     
?>