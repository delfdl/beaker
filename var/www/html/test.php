<?php 


function iso6391to6392($iso6391)
 {
$code2ToName = array(
        'ar' => 'arabic',
        'az' => 'azeri',
        'bg' => 'bulgarian',
        'bn' => 'bengali',
        'ch' => 'chinese',
        'cs' => 'czech',
        'cy' => 'welsh',
        'da' => 'danish',
        'de' => 'german',
        'en' => 'english',
        'es' => 'spanish',
        'el' => 'greek',
        'et' => 'estonian',
        'fa' => 'farsi',
        'fi' => 'finnish',
        'fr' => 'french',
        'ha' => 'hausa',
        'he' => 'hebrew',
        'hi' => 'hindi',
        'hr' => 'croatian',
        'hu' => 'hungarian',
        'id' => 'indonesian',
        'is' => 'icelandic',
        'it' => 'italian',
        'ja' => 'japanese',
        'jp' => 'japanese',
        'kk' => 'kazakh',
        'ky' => 'kyrgyz',
        'la' => 'latin',
        'lt' => 'lithuanian',
        'lv' => 'latvian',
        'mk' => 'macedonian',
        'mn' => 'mongolian',
        'ne' => 'nepali',
        'nl' => 'dutch',
        'no' => 'norwegian',
        'pl' => 'polish',
        'ps' => 'pashto',
        'pt' => 'portuguese',
        'ro' => 'romanian',
        'ru' => 'russian',
        'sk' => 'slovak',
        'sl' => 'slovene',
        'so' => 'somali',
        'sq' => 'albanian',
        'sr' => 'serbian',
        'sv' => 'swedish',
        'sw' => 'swahili',
        'tl' => 'tagalog',
        'tr' => 'turkish',
        'uk' => 'ukrainian',
        'ur' => 'urdu',
        'uz' => 'uzbek',
        'vi' => 'vietnamese',
        'zh' => 'chinese',
        'und' => 'und',
    );
$nameToCode3 = array(
        'und'   => 'und',
        'albanian'   => 'sqi',
        'arabic'     => 'ara',
        'azeri'      => 'aze',
        'bengali'    => 'ben',
        'bulgarian'  => 'bul',
        'cebuano'    => 'ceb',
        'chinese'    => 'zho',
        'croatian'   => 'hrv',
        'czech'      => 'ces',
        'danish'     => 'dan',
        'dutch'      => 'nld',
        'english'    => 'eng',
        'estonian'   => 'est',
        'farsi'      => 'fas',
        'finnish'    => 'fin',
        'french'     => 'fra',
        'german'     => 'deu',
        'greek'      => 'gre',
        'hausa'      => 'hau',
        'hawaiian'   => 'haw',
        'hebrew'     => 'heb',
        'hindi'      => 'hin',
        'hungarian'  => 'hun',
        'icelandic'  => 'isl',
        'indonesian' => 'ind',
        'japanese'   => 'jpn',
        'italian'    => 'ita',
        'kazakh'     => 'kaz',
        'kyrgyz'     => 'kir',
        'latin'      => 'lat',
        'latvian'    => 'lav',
        'lithuanian' => 'lit',
        'macedonian' => 'mkd',
        'mongolian'  => 'mon',
        'nepali'     => 'nep',
        'norwegian'  => 'nor',
        'pashto'     => 'pus',
        'pidgin'     => 'crp',
        'polish'     => 'pol',
        'portuguese' => 'por',
        'romanian'   => 'ron',
        'russian'    => 'rus',
        'serbian'    => 'srp',
        'slovak'     => 'slk',
        'slovene'    => 'slv',
        'somali'     => 'som',
        'spanish'    => 'spa',
        'swahili'    => 'swa',
        'swedish'    => 'swe',
        'tagalog'    => 'tgl',
        'turkish'    => 'tur',
        'ukrainian'  => 'ukr',
        'urdu'       => 'urd',
        'uzbek'      => 'uzb',
        'vietnamese' => 'vie',
        'welsh'      => 'cym',
    );
    
    $tmp = 'und';
   // echo "iso6391: ".$iso6391."<br />";
   echo 'checking on '.$iso6391.'<br /><br />';
   
    if (strlen($iso6391)==2)
    {
        if (isset($code2ToName[$iso6391])) 
        {
             $tmp = $code2ToName[$iso6391];
        } 
        else 
        {
        $tmp = 'und';
        }
    //    echo "tmp: ".$tmp."<br />";
        $tmp2 = 'und';  
        $tmp2 = $nameToCode3[$tmp];
    //    echo "tmp2: ".$tmp2."<br />";
    
        $iso6392 = $tmp2; // 3 char iso language
    }
    else 
    {
        foreach($nameToCode3 as $key => $wrongCode)
        {
            echo '<br />'.$iso6391.' in '.$key.' ';
         
            if (substr($key,0,3)==$iso6391)
            {
                echo '*';
            }

           
        }
        echo $tmpfred;
    }
 return $iso6392 ;   
 }
 
 $fred = 'dut';
 $newfred = iso6391to6392($fred);
 echo $newfred;
 ?>
