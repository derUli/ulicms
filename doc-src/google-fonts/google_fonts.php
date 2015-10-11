<?php
$xml = file_get_contents("webFontNames.opml");
$xml =  new SimpleXMLElement($xml);
var_dump($xml);
