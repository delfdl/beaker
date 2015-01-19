<?php  
function xml2jonah($assetxml) 					// receives filename (e.g. mtv-rihanna_where_have_you_been_720.mp4)
{
$tmpassetxml = ; // remove file suffix
$assetxml = 	$pathtoxml.; // 

$doc = new DOMDocument(); 
$doc->load($tmpassetxml);//xml file loading here 
 
$employees = $doc->getElementsByTagName( "employee" ); 
foreach( $employees as $employee ) 
{ 
  $names = $employee->getElementsByTagName( "description" ); 
  $name = $names->item(0)->nodeValue; 
 
  echo "<b>$name - $age - $salary\n</b><br>"; 
  } 
}
?> 