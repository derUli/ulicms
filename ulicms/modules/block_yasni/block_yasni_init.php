<?php
@$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

if($hostname){
     $hostname = strtolower($hostname);
    
     $list = array("123people.de", "findestars.de", "myonid.de",
         "peekyou.com", "pipl.com", "rapleaf.com", "snitch.name",
         "spock.com", "tweepz.com", "wink.com", "yasni.de",
         "yoname.com", "yourtraces.com", "zoominfo.com");
    
     for($s = 0; $s < count($list); $s++){
         if(endsWith($hostname, $list[$s])){
             die("Respect our Privacy");
             }
         }
    
     }
?>