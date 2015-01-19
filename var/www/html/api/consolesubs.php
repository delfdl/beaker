<?php 
mb_internal_encoding("UTF-8");
     ini_set ("display_errors", "1");
     error_reporting(E_ALL);
     $srt = $_GET["srt"];
     $path = $_GET["path"];
     
     if (isset($_GET["debug"])) 
     {
         $debug = $_GET["debug"];
     }
     else 
     {$debug=0;}
     
    // echo 'debug: '.$debug.'<br />';
     
define ('UTF32_BIG_ENDIAN_BOM'   , chr(0x00).chr(0x00).chr(0xFE).chr(0xFF));
define ('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF).chr(0xFE).chr(0x00).chr(0x00));
define ('UTF16_BIG_ENDIAN_BOM'   , chr(0xFE).chr(0xFF));
define ('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF).chr(0xFE));
define ('UTF8_BOM'               , chr(0xEF).chr(0xBB).chr(0xBF));

 function detect_utf_encoding($filename) 
 {
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

$formContents['srt']    = $srt;
$formContents['charset']= $enctype; 
$formContents['bom']    = $subtype; 
$formContents['assoc']  = $path;
$formContents['debug']  = $debug; 
$formContents = json_encode($formContents);

        $ch = curl_init('http://ingest2.ot2.tv/tools/associate3.php'); 
        $timeout = 5; 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $formContents);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Content-Length: ' . strlen($formContents), 
        'charset='.$enctype
        ));                                                                                                                   
        $result = curl_exec($ch); 
        curl_close($ch);

// echo ('done - '.$formContents);
if ($debug==1)
{
    echo ('[debug] <br />'.$result);
}
