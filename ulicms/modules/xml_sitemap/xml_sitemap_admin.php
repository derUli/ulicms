<?php 
define("MODULE_ADMIN_HEADLINE", "XML Sitemap");

$required_permission = getconfig("xml_sitemap_required_permission");

if($required_permission === false){
   $required_permission = 40;
}


function getBaseURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "https";}
 $pageURL .= "://";
 $dirname = str_replace("admin", "", dirname($_SERVER["REQUEST_URI"]));
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


define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);

if(isset($_POST["submit"]) and has_permissions(40)){
   $xml_string = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

  $query_pages = mysql_query("SELECT * FROM ".tbname("content")." WHERE active = 1 ORDER by lastmodified");
  while($row = mysql_fetch_object($query_pages)){
     $xml_string .= "<url>
	 ";
     $xml_string .= "<loc>".getBaseURL().$row->systemname.".html"."</loc>
	 ";
	 $xml_string .= "<lastmod>".date("Y-m-d", $row->lastmodified)."</lastmod>
	 ";
     $xml_string .= "</url>
	 ";
  }


  $xml_string .= "</urlset>";
  $xml_string = str_replace("\r\n", "\n", $xml_string);
  $xml_string = str_replace("\r", "\n", $xml_string);
  $xml_string = str_replace("\n", "\r\n", $xml_string);
  echo nl2br(htmlspecialchars($xml_string));
}

// Konfiguration checken
$send_comments_via_email = getconfig("blog_send_comments_via_email") == "yes";

function xml_sitemap_admin(){
?>

<form action="<?php echo getModuleAdminSelfPath()?>" method="post">
<input type="submit" name="submit" value="Sitemap generieren"/>
</form>
<?php
}
 
?>
