<?php 
  //    Data, connection, auth
     
        $soapUrl = "http://head1.ot2.tv/balancerSOAP"; // asmx URL of WSDL

  //    xml post structure
       
        $xml_post_string = '
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns="urn:envivio:balancer:1.0">
  <soap:Header/>
  <soap:Body>
    <getAllResources/>
  </soap:Body>
</soap:Envelope>'; // 


           $headers = array(
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "SOAPAction: http://head1.ot2.tv/balancerSOAP/getAllResources", 
                        "Content-length: ".strlen($xml_post_string),
                    ); //SOAPAction: your op URL

            $url = $soapUrl;

            // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // converting
            $response = curl_exec($ch); 
            curl_close($ch);
            
            echo ("REQUEST: <br /><div align='center' style='align:center;'><form><textarea id='showText' class='showText' cols='75' rows='20' style='overflow:auto;'>".$xml_post_string."</textarea></form></div>");        
            
           echo ("RESPONSE: <br /><div align='center' style='align:center;'><form><textarea id='showText' class='showText' cols='75' rows='20' style='overflow:auto;'>".$response."</textarea></form></div>");

            // converting
            //$response1 = str_replace("<resources>","",$response);
            
            $response1 = strstr($response,'<resources>');
            $response2 = strstr($response1,'</resources>',1);
            $resourcecount = substr_count($response2,'name=');
            echo ('resourcecount: '.$resourcecount.'<br />'); 
                       
            $count = explode('</resource><resource>',$response2);

            foreach($count as $resource)
            {
               echo '<textarea cols=100 rows=3>'.$resource.'</textarea>';
               $encname2 = strstr($resource,'name=');
               $encname3 = str_replace('name="','',$encname2);
               $encname = strstr($encname3,'"',1);

               
               $encip2 = strstr($resource,'url=');
               $encip3 = str_replace('url="','',$encip2);
               $encip = strstr($encip3,'"',1);
               
               echo $encname.' | '.$encip.'<br />';
              
            }
            
        
           


//foreach($xml->xpath('//soap:Body') as $header) {
  //  echo (string)$header->LoginResult;
//}

            
          
            
             
            // user $parser to get your data out of XML response and to display it.
?>
