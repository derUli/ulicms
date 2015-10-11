<?php
$xml = file_get_contents("webFontNames.opml");
$xml =  new SimpleXMLElement($xml);
foreach($xml->body->outline as $outline){
   echo $outline["text"];
   echo "\r\n";
};
