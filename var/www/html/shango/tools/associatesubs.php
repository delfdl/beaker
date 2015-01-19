<?php
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $srt = $_GET["srt"];
     
define ('UTF32_BIG_ENDIAN_BOM'   , chr(0x00).chr(0x00).chr(0xFE).chr(0xFF));
define ('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF).chr(0xFE).chr(0x00).chr(0x00));
define ('UTF16_BIG_ENDIAN_BOM'   , chr(0xFE).chr(0xFF));
define ('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF).chr(0xFE));
define ('UTF8_BOM'               , chr(0xEF).chr(0xBB).chr(0xBF));

 function detect_utf_encoding($filename) {

     $text = file_get_contents($filename);
     $first2 = substr($text, 0, 2);
     $first3 = substr($text, 0, 3);
     $first4 = substr($text, 0, 3);
     
     if ($first3 == UTF8_BOM) return 'UTF8_BOM';
     elseif ($first4 == UTF32_BIG_ENDIAN_BOM) return 'UTF-32BE';
     elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM) return 'UTF-32LE';
     elseif ($first2 == UTF16_BIG_ENDIAN_BOM) return 'UTF-16BE';
     elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM) return 'UTF-16LE';
     else return 'none';
 }     
$info = file_get_contents($srt);
$enctype =  mb_detect_encoding($info);
$subtype = detect_utf_encoding($srt);

echo ("<br />Associating '<i>".$srt."'</i> with ...<br />");
echo ("<br /><form name='srtAssociation' action='/tools/associate2.php' method='post' accept-charset='".$enctype."'>");
echo ("<input type='hidden' name='srt' value='".$srt."' />");


// echo " | Detected charset: ".$enctype." | ".$subtype."<br /><br />";
echo ("CHARSET: <input name='charset' value='".$enctype."' />&nbsp;");
echo ("BOM: <input name='bom' value='".$subtype."' /><br /><br />");

echo ("<textarea cols='140' rows='18' style='font-size:8pt; padding:5px;'>");

     // $info = file($srt);   // best way to load file?                           

       //  foreach ($info as $infoArray)
       //  {
echo $info;
       //      $infoArray = str_replace(PHP_EOL, '', $infoArray);
       //      //echo $info_line;
       //      if (strlen($infoArray)==1) 
       //      {
       //        // echo ('\n'.$infoArray);
       //      } 
       //      else 
       //      {
       //         echo ($infoArray);
       //      } 
                            
       //  }

echo ("</textarea><br /><br />");

//echo ("<select name='assoc'>");
// echo ("<option value='".$hashNo."'>".$hashNo."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$shortName." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(".$ingestInception.")"."</option>");      
//echo ("</select>");

echo ("<input name='assoc' value='/mount/wing/vod/org/channel/item/' size='80' />&nbsp;&nbsp;");
echo ("&nbsp;&nbsp;<input type='submit' value=' | attach | ' /></form>");   

?>
<br />
<div id="innertxtHint" style='padding:10px;border:1px solid silver;' class='ui-corner-all shadow'>

