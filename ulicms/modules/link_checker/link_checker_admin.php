<?php 
define("MODULE_ADMIN_HEADLINE", "Link Checker");

$required_permission = getconfig("link_checker_required_permission");

if($required_permission === false){
   $required_permission = 20;
}

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);


function get_http_response_code($theURL) {
    $headers = get_headers($theURL);
    return substr($headers[0], 9);
}

include_once "../templating.php";


function link_checker_admin(){
?>

<p><a href="index.php?action=module_settings&module=link_checker&show=all">Alle</a> | 
<a href="index.php?action=module_settings&module=link_checker">Nur Fehler</a>  | 
<a href="index.php?action=module_settings&module=link_checker&show=404">Nicht gefunden</a> | 
<a href="index.php?action=module_settings&module=link_checker&show=redirection">Umleitungen</a></p>
<?php
   $query = mysql_query("SELECT content FROM ".tbname("content"));
   $hasLinks = false;
   while($row = mysql_fetch_object($query)){
      $htmldatei = $row->content;
      $htmldatei = replaceShortcodesWithModules($htmldatei);
      $htmldatei = apply_filter($htmldatei, "content");
      
       preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $htmldatei, $links);
       for($i = 0; $i < count($links); $i++){
          if(!empty($links[0][$i])){
             $status = get_http_response_code($links[0][$i]);
             
                  
              if($_GET["show"] == "all"){
                  $hasLinks = true;
                  echo "<p>".$links[0][$i]." [".htmlspecialchars($status, ENT_QUOTES, "UTF-8")."]"."</p>";
              }
              else if($_GET["show"] == "redirection" and ($status == "301 Moved Permanently" or $status == "302 Found" or $status == "302 Moved Temporarily")){
                  $hasLinks = true;
                  echo "<p>".$links[0][$i]." [".htmlspecialchars($status, ENT_QUOTES, "UTF-8")."]"."</p>";
              }
              else if($_GET["show"] == "404" and $status === "404 Not Found")
              {
                  $hasLinks = true;
                  echo "<p>".$links[0][$i]." [".htmlspecialchars($status, ENT_QUOTES, "UTF-8")."]"."</p>";
              }
              else if($status != "200 OK" and !isset($_GET["show"])){
                  $hasLinks = true;
                  echo "<p>".$links[0][$i]." [".htmlspecialchars($status, ENT_QUOTES, "UTF-8")."]"."</p>";
              }
              
      
    }
    
    }
    

      
}

    if(!$hasLinks){
       echo "<p>Keine Links vorhanden</p>";
    }

}
   
   
   
?>
   
