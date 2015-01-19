<?php 
  //    Data, connection, auth
        $inputfilename = $_POST['inputfilename'];   // request data from the form
        $outputfilename = $_POST['outputfilename']; // request data from the form
        $jobname = $_POST['jobname'];               // request data from the form
        $presetid = $_POST['presetid'];             // request data from the form
        
        $soapUrl = "http://head1.ot2.tv/balancerSOAP"; // asmx URL of WSDL
  //    $soapUser = "username";  //  username
  //    $soapPassword = "password"; // password
        
  //    $inputfilename='file://192.168.0.6/secure/delstest/firework.mp4';
  //    $outputfilename='file://nasa/wing/vod/delstest/deltest1234';
  //    $presetid='muse4b';

  //    xml post structure
  
  

        
        
        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns="urn:envivio:balancer:1.0"> 
    <soap:Header/> 
    <soap:Body> 
        <launchJob> 
            <presetid>'.$presetid.'</presetid> 
            <jobname>'.$jobname.'</jobname> 
             <jobparams>
                <param name="inputfilename" value="'.$inputfilename.'"/> 
                <param name="outputfilename" value="'.$outputfilename.'"/> 
             </jobparams>
        </launchJob> 
     </soap:Body>
 </soap:Envelope>';   // data from the form, e.g. some ID number

           $headers = array(
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "SOAPAction: http://head1.ot2.tv/balancerSOAP/launchJob", 
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
            $response1 = str_replace("<soap:Body>","",$response);
            $response2 = str_replace("</soap:Body>","",$response1);

            // convertingc to XML
            $parser = simplexml_load_string($response2);
            // user $parser to get your data out of XML response and to display it.
?>
