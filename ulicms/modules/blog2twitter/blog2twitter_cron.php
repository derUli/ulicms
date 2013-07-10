<?php 

if(!function_exists("get_requested_pagename") and !is_admin_dir())
   include_once "templating.php";

if(!function_exists("rootDirectory")){
function rootDirectory() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "https";}
 $pageURL .= "://";
 $dirname = dirname($_SERVER["REQUEST_URI"]);
 $dirname = str_replace("\\", "/", $dirname);
 $dirname = trim($dirname, "/");
 if($dirname != ""){
    $dirname = "/".$dirname."/";
 } else {
   $dirname = "/";
 }
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$dirname;
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$dirname;
 }
 return $pageURL;
}

}


include_once getModulePath("blog2twitter")."api/twitter.class.php";

if($_GET["blog_admin"] == "submit"){
if(!empty($_POST["title"]) and isset($_POST["seo_shortname"])){
   
   $title = $_POST["title"];
   $seo_shortname = $_POST["seo_shortname"];

   $link = rootDirectory().get_requested_pagename().".html?single=".$seo_shortname;
   
   if($_POST["entry_enabled"] == "1"){
   
     $consumerKey = getconfig("blog2twitter_consumer_key");
     $consumerSecret = getconfig("blog2twitter_consumer_secret");
     
     $accessToken = getconfig("blog2twitter_access_token");
     $accessTokenSecret = getconfig("blog2twitter_access_token_secret");
     
     $post = $link;
          
     if($consumerKey !== false && $consumerSecret !== false && 
       $accessToken !== false &&  $accessTokenSecret !== false){
       $twitter = new Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
       $status = $twitter->send($post);
      
       setconfig("blog2twitter_status", $status ? 'Funktioniert!' : '<strong>Fehler beim übertragen!</strong>\nEventuell Zugangsdaten inkorrekt?');

      } else {
        setconfig("blog2twitter_status", "<strong>Fehlende Zugangsdaten.</strong>\nMehr Informationen siehe liesmich.txt im Modulordner.");
      }
   
   }
   
}

}



?>