<?php
include_once "init.php";

function enforce_www($doit = true){
   if($doit){
      $domain = get_domain();
	  if(substr_count($domain, ".") == 1){
	     $new_url = get_site_protocol () . "www.".get_domain () . get_request_uri();
	  }
   } else {
       $domain = get_domain();
	    if(substr_count($domain, ".") == 2 and startsWith($domain, "www.")){
		 $domain = substr($domain, 4);
	     $new_url = get_site_protocol () . $domain . get_request_uri();
	  }
   }
   ulicms_redirect($new_url);
}

enforce_www(false);