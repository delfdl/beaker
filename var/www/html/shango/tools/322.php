<?
$testhash = 'ott322_fa7867c673e8a7fd80d66e38c894956f';                             
$url = 'http://vod1.ot2.tv/vod/otv/'.$testhash.'/'.$testhash.'.ism/'.$testhash.'.f4m';
$prurl = 'http://vod1.ot2.tv/vod/otv/'.$testhash.'/'.$testhash.'.ism/Manifest';
                           
// http://live1.ot2.tv/uspgui/osmf.php?streamURL=http://live.ot2.tv/live/skynews/skynews.isml/skynews.f4m

// analysis of the generated .ism manifest

$hash = $testhash;

$ism_src = '/mount/wing/vod/otv/'.$hash.'/'.$hash.'.ism';        // generate path to src file (rewrite for org/channel when it finally happens)
$playout = 'http://vod.ot2.tv/vod/otv/'.$hash.'/'.$hash.'.ism';  // generate path to load ism in browser 
if (file_exists($ism_src))
{
    $handle = fopen($ism_src,'r');                  
    $contents = stream_get_contents($handle);           // load file as string
    fclose($handle);
} else {echo('ism manifest not found at '.$ism_src.'<br /><br />');}

    $videoCount = substr_count($contents,"video src");
    $audioCount = substr_count($contents,"audio src");
    $textCount  = substr_count($contents,"textstream src");
    
    echo("<div id='qcism' style='border:1px solid silver; padding:5px; height:200px; width:600;margin-left:20px;' class='ui-corner-all'><br />Source: <i>".$hash.".ism</i><br /><br />");

if (file_exists($ism_src))
{   
    $xml = simplexml_load_file($ism_src);               // load file as object
} else {echo('ism manifest not found at '.$ism_src.'<br /><br />');}
        
    echo("Video Summary: <b>".$videoCount."</b> tracks > ");
    $videoType='SD';
    foreach ($xml->body->switch->video as $videotrack)
    {
    // $bitrate   = ((string)$videotrack['systemBitrate']) ;
    // $bitrate2  = $bitrate/1000;
    // $bitrate   = $bitrate2."k";
    $Maxwidth  = $videotrack->param[2];                  // width
    $width     = ((string)$Maxwidth['value']);
    $Maxheight = $videotrack->param[3];                  // height
    $height    =((string)$Maxheight['value']);
    
    echo (" ".$width."x".$height." |");
    }
    if ($height>576) {$videoType='HD';}
    echo ("| <b>".$videoType."</b>");
    echo ("<br />");  
 
    echo("Audio Summary: <b>".$audioCount."</b> tracks > ");

    foreach ($xml->body->switch->audio as $audiotrack)
    {
    $language = ((string)$audiotrack['systemLanguage']) ;
    if ($language='') {$language='undeclared';}
    $bitrate = ((string)$audiotrack['systemBitrate']) ;
    $bitrate2 = $bitrate/1000;
    $bitrate = $bitrate2."k";
    $channels = $audiotrack->param[3];                  // no of channels
    $channelCount=((string)$channels['value']);
    
        $channelType = $channelCount;
        if ($channelCount=='1') {$channelType='mono';}
        if ($channelCount=='2') {$channelType='stereo';}
        if ($channelCount=='6') {$channelType='5.1';}
    
    echo (" <b>".$language."</b> (".$bitrate." ".$channelType.") ");
    }
    
    echo ("<br />");
    if ($textCount==0) {$subspresent='no';} else {$subspresent='yes';}
    echo("Subtitled: ".$subspresent.", <b>".$textCount."</b><br /><br />");
    echo ("<br clear='all' />");
    
    echo("</div><br clear='all' />");


echo("Preview <a href='http://live1.ot2.tv/uspgui/osmf.php?streamURL=".$url."'>http://live1.ot2.tv/uspgui/osmf.php?streamURL=".$url."</a><br /><br />");
echo("Preview <a href='http://live1.ot2.tv/usp-evaluation/silver.html?file=".$prurl."'>http://live1.ot2.tv/usp-evaluation/silver.html?file=".$prurl."</a><br /><br />");

     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     // $hash = $_GET["hash"];
//$hash = urlencode($hash);
$url = 'http://live.ot2.tv/tools/api/showfiles.php?hash='.$hash; 
    $ch = curl_init(); 
    // echo ("initiating ".$url."<br />");
    $timeout = 5; 
    curl_setopt($ch,CURLOPT_URL,$url); 
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
    $response = curl_exec($ch); 
    curl_close($ch); 
 
echo($response);
$url2 = 'http://vod1.ot2.tv/vod/otv/'.$testhash.'/test.ism/test.f4m'; 
$prurl2 =  'http://vod1.ot2.tv/vod/otv/'.$testhash.'/test.ism/Manifest'; 
echo("<br />Preview <a href='http://live1.ot2.tv/uspgui/osmf.php?streamURL=".$url2."'>http://live1.ot2.tv/uspgui/osmf.php?streamURL=".$url2."</a><br />");
echo("<br />Preview <a href='http://live1.ot2.tv/usp-evaluation/silver.html?file=".$prurl2."'>http://live1.ot2.tv/usp-evaluation/silver.html?file=".$prurl2."</a><br /><br />");
?>