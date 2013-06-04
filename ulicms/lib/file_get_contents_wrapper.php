<?php 
// die Funktionalität von file_get_contents
// mit dem Curl-Modul umgesetzt
function file_get_contents_curl($url) {
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
	curl_setopt($ch, CURLOPT_URL, $url);
	
	$data = curl_exec($ch);
	
	if(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200 and
	curl_getinfo($ch, CURLINFO_HTTP_CODE) != 304){
             $data = false;
	}
	
	curl_close($ch);
	return $data;
}

function is_url($url){
  if(substr_compare($url, 'http://', 0, 7) > 0 or substr_compare($url, 'https://', 0, 8) > 0 or substr_compare($url, 'ftp://', 0, 8) > 0){
      return true;
  }
  
  
  return false;
  
}


// Wrapper um file_get_contents
// Falls allow_url_fopen deaktiviert ist,
// wird CURL alls Fallback genutzt, falls vorhanden.
// Ansonsten wird false zurückgegeben.
function file_get_contents_wrapper($url){
   if(ini_get("allow_url_fopen") or !is_url($url)){
   $config = new config();
   if(!isset($config->proxy))
     return file_get_contents($url);
   else
     return file_get_contents_proxy($url, $config->proxy);
   } else {
     if(function_exists("curl_init") and is_url($url)){
       return file_get_contents_curl($url);
     } 

   }
   
   return false;
}

// file_get_contents durch Proxy
function file_get_contents_proxy($url,$proxy){

    // Create context stream
    $context_array = array('http'=>array('proxy'=>$proxy, 
    'request_fulluri'=>true));
    $context = stream_context_create($context_array);

    // Use context stream with file_get_contents
    $data = file_get_contents($url,false,$context);

    // Return data via proxy
    return $data;

}