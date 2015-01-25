<?php

// String contains chinese chars?
function is_chinese($str)
{
    return preg_match("/\p{Han}+/u", $str);
    }

// checking if this Country is blocked by spamfilter
function isCountryBlocked(){
    
     $country_blacklist = getconfig("country_blacklist");
     $country_whitelist = getconfig("country_whitelist");
    
     $country_blacklist = str_replace(" ", "", $country_blacklist);
     $country_whitelist = str_replace(" ", "", $country_whitelist);
    
     $country_blacklist = strtolower($country_blacklist);
     $country_whitelist = strtolower($country_whitelist);
    
     $country_blacklist = explode(",", $country_blacklist);
     $country_whitelist = explode(",", $country_whitelist);
    
     $ip_adress = $_SERVER["REMOTE_ADDR"];
    
     @$hostname = gethostbyaddr($ip_adress);
    
    
     if(!$hostname){
         return false;
         }
    
     $hostname = strtolower($hostname);
    
     for($i = 0; $i < count($country_whitelist); $i++){
         $ending = "." . $country_whitelist[$i];
         if(EndsWith($hostname, $ending)){
             return false;
             }
         }
    
     for($i = 0; $i < count($country_blacklist); $i++){
         $ending = "." . $country_blacklist[$i];
         if(EndsWith($hostname, $ending)){
             return true;
             }
        
         }
    
    
     return false;
    
     }

function trackbackSpamCheck($url)
{
     // trackback prinzipiell als spam definieren
    $spam = TRUE;
    
     // URL in einzelteile zerlegen
    $url = parse_url(trim(addSlashes($url)));
    
     // verbindung zum host auf port 80 herstellen
    $fp = fSockOpen($url['host'], 80, $errno, $errstr, 30);
    
     // ueberpruefen, ob die verbindung steht
    if($fp)
    {
         // pfad zur zieldatei auslesen
        $path = $url['path'];
         if(isSet($url['query']))
            {
             $path .= "?" . $url['query'];
             }
        
         // wenn der pfad leer ist '/' verwenden
        if($path == "")
        {
             $path = "/";
             }
        
         // get request an den server senden
        $req = "GET " . $path . " HTTP/1.0\r\n";
         $req .= "Host: " . $url['host'] . "\r\n\r\n";
         fPuts($fp, $req);
        
         // http headers auslesen
        while(!feof($fp))
        {
             $data = fgets($fp, 1024);
             if(trim($data) == "")
                {
                 break;
                 }
             }
        
         // daten auslesen
        while(!feof($fp))
        {
             $data = fgets($fp, 1024);
            
             // ueberpruefen, ob t-error.ch darin vorkommt
            // dies kann man noch verfeinern,
            // in dem man nach einem link sucht
            if(eregi("t-error.ch", $data))
                {
                 $spam = FALSE;
                 break;
                 }
             }
        
         // verbindung zum server trennne
        fclose($fp);
         }
    
     // ip adresse des hosts auslesen,
    // auf dem die im trackback angegebene
    // webseite liegt
    $ip = @getHostByName($url['host']);
    
     // ueberpruefen, ob die ip adresse
    // aufgeloest werden konnte.
    // trackback sonst als spam definieren
    if($ip == $url['host'])
    {
         $spam = TRUE;
         }
    else
        {
         // ip adresse der webseite
        // mit der ip des hosts,
        // welcher den trackback gesendet
        // hat vergleichen
        if($_SERVER['REMOTE_ADDR'] != $ip)
        {
             $spam = TRUE;
             }
         }
    
     // spam status zurueckgeben
    return $spam;
     }
 