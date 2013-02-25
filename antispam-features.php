<?php 
function isBlocked(){

	$country_blacklist = getconfig("country_blacklist");
	$country_whitelist = getconfig("country_whitelist");
	
	$country_blacklist = str_replace(" ", "", $county_blacklist);
	$country_whitelist = str_replace(" ", "", $county_whitelist);
	
	$country_blacklist = explode(",", $country_blacklist);
	$country_whitelist = explode(",", $country_whitelist);
	
	$ip_adress = $_SERVER["REMOTE_ADDR"];
	
	@$hostname = gethostbyaddr($ip_adress);
	
	if(!$hostname){
           return false;	
	}
	
	for($i=0; $i < size($country_whitelist); $i++){
	  $ending = ".".$country_whitelist[$i];
          if(EndsWith($hostname, $ending){
             return false;         
          }
	}
	
	for($i=0; $i < size($country_blacklist); $i++){
	  $ending = ".".$country_blacklist[$i];
          if(EndsWith($hostname, $ending){
             return true;         
          }
          
     
	}
	
	
	return false;
	
}
?>