<?
// $hash = $_GET["file_id"]; 
if (isset($_GET["file_id"])) {$hash = $_GET["file_id"];} else {$hash  = 'null';} 
if (isset($_GET["debug"]))   {$debug = $_GET["debug"];}  else {$debug = 0;}                           // get file id
                                                                                 
function thumbGen($inputVOD,$thumbTime,$outURL)
     {  
        $selectI =" -vf select='eq(pict_type\,I)'";
        $fireandforget = '';
        $ffmpegThumb = "ffmpeg -i '".$inputVOD."' -vframes 1 -s 240x135 ".$selectI." -ss ".$thumbTime." -f image2 '".$outURL."'"; // kick off thumbgen
        // echo "Executing: <br /><i>". $ffmpegThumb."</i><br /><br />" ;
        $tmp = exec($ffmpegThumb);
    return $tmp;
     }
     
// analysis of the ffmpeg output

    echo("<div id='qcffmpeg' style='border:1px solid silver; padding:5px; display:inline-block; height:300px; width:600; float:left;' class='ui-corner-all'><br />Source: <i>".$hash.".mp4</i><br /><br />");

$ffmpegpath = '/var/www/qc/';
$srcpath = '/home/secure/'.$hash.'.mp4';
$thumbPath = '/var/www/thumbs/'.$hash.'.jpg';

if (!file_exists($srcpath)) 
{echo ('physical file at '.$srcpath.' not found<br />');} 
else 
{
    if (!file_exists($thumbPath))                               // make thumbnail if no thumbnail found
    {
        $makeThumb = thumbGen($srcpath,120,$thumbPath); 
    }
}
$qcpath = $ffmpegpath.$hash.'.txt';
if (!file_exists($qcpath))                                      // check to see if ffmpeg output already exists 
    {   
       //echo ('analysis not found, '); 
       $ffmpegCommand="ffmpeg -i '".$srcpath."' 2> '".$qcpath."'";                                               // build ffmpeg exec command
       //echo (' running exec('.$ffmpegCommand.')<br />'); 
       $response = exec($ffmpegCommand);            // run ffmpeg
    }  

    //echo ('loading '.$qcpath.'<br />'); 
    $ffmpeg_array = file($qcpath);
    $howmany=count($ffmpeg_array);
   
    $i=0;
    $totalaudioStreams = 0;
    $totaltextStreams = 0;
    
    foreach ($ffmpeg_array as $ffmpeg_key)
    {
         if ((strpos($ffmpeg_key,"Audio")) && (strpos($ffmpeg_key,"Stream"))) 
             {
                //echo ("<b>audio found - [".$i."]</b> - ".$ffmpeg_key."<br />");  
                $totalaudioStreams++; 
             }
    }
    
     foreach ($ffmpeg_array as $ffmpeg_key)
    {
        if ((strpos($ffmpeg_key,"Subtitle")) && (strpos($ffmpeg_key,"Stream"))) 
             {
                //echo ("<b>subtitle found - [".$i."]</b> ".$ffmpeg_key."<br />"); 
                $totaltextStreams++; 
              }
    }   

    
    $i=0;
    $audioStreams = 0;
    $textStreams = 0;
        
    foreach ($ffmpeg_array as $ffmpeg_key)
      {
        
       if (strpos($ffmpeg_key,"No such file"))
            {
                echo ('Source file not found at:<br /><i>'.$srcpath.'</i><br />');
            }  
     
        if ((strpos($ffmpeg_key,"Video")) && (strpos($ffmpeg_key,"Stream"))) 
             {
                echo ("<b>video stream</b> ".$ffmpeg_key."<br />");  
             }
     
     
        if ((strpos($ffmpeg_key,"Audio")) && (strpos($ffmpeg_key,"Stream"))) 
             {
                echo ("<b>audio stream</b> ".$ffmpeg_key."<br />");  
             }
     
        if ((strpos($ffmpeg_key,"Subtitle")) && (strpos($ffmpeg_key,"Stream"))) 
             {
                echo ("<b>subtitle stream</b> ".$ffmpeg_key."<br />"); 
              }
     
        } // end foreach ffmpeg_key
        
        echo ("<br /><b>".$totalaudioStreams." audio streams found</b><br />");
        echo ("<br /><img src='/thumbs/".$hash.".jpg' width='240' height='135' style='ui-corner-all shadow' /><link href='/jonahv1/css/main.css'><br />");        

    echo("</div>");

 if ($debug==1)                         // show all ffmpeg output if debug set
    {
    $i=0;
    echo ("<div style='float:left;'>");
    foreach ($ffmpeg_array as $ffmpeg_key)
         {
         $i++;
         echo ("<b>[".$i."]</b> ".$ffmpeg_key."<br />");
         }
    echo ("</div>");
    }
    
 
 
 
 
    


// analysis of the generated .ism manifest

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
    
    echo("<div id='qcism' style='border:1px solid silver; padding:5px; display:inline-block; height:300px; width:600;margin-left:20px;' class='ui-corner-all'><br />Source: <i>".$hash.".ism</i><br /><br />");

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
    echo ("<br clear='all' /><div id='playoutDiv'>");
    echo ("<a href='http://live1.ot2.tv/usp-evaluation/silver.html?file=".$playout."/Manifest'>HSS</a> | ");
    echo ("<a href='http://live1.ot2.tv/usp-evaluation/silver.html?file=".$playout."/Manifest'>HDS</a>");
    echo ("</div>");

    
    echo("</div>");
    

?>