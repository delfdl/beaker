<?php
require_once('class.mediaInfo.php');

// give here your own video/movie file
$mi = new mediaInfo('/home/secure/test/surfing.265');

// get the filesize
echo('Filesize: '.$mi->get_file_size().'<br>');

// get the format
echo('Format: '.$mi->get_general_property('Format').'<br>');

// get the duration
echo('Duration: '.$mi->get_general_property('Duration').'<br>');


// get the aspect ratio
echo('Aspect ratio: '.$mi->get_video_property('Display aspect ratio').'<br>');

// get the aspect ratio
echo('Audio format: '.$mi->get_audio_property('Format').'<br>');

// get the all general info
echo($mi->print_media_info().'<br>');
?>