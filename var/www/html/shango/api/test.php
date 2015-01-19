<?php
function processCount() 
    {
$commandtoUse = 'ps -f -C ffmpeg';
// $commandtoUse = 'ps -eo pid,%cpu,%mem,ffmpeg,www-data | sort -k 2nr | head';
exec($commandtoUse, $fred);

       // echo 'fred: '.$fred.'<br />';
 echo ('<table>');
 echo ('<tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>');      
       $arrayCount = count($fred);
       echo ('<tr>');
        for ($loop = 1; $loop < $arrayCount; $loop++)
        {  
            $component='';
            echo ('<td>'.$loop.': '.$fred[$loop].'</td>');
         
                if (strpos($fred[$loop],'video transcode'))
                {
                $component = 'video transcode';
                }
                if (strpos($fred[$loop],'audio transcode'))
                {
                $component = 'audio transcode';
                }
                if (strpos($fred[$loop],'thumb transcode'))
                {
                $component = 'thumbnail';
                } 
                echo ('<td>'.$component.'</td>');                       
 
        echo ('</tr>');
        } 
 
       
 echo ('</table>');       
        
        
    }
processCount();
echo ('---------------------<br /><br />');





?>
