<?php
define("MODULE_ADMIN_HEADLINE", "XML Sitemap");

$required_permission = getconfig("xml_sitemap_required_permission");

if($required_permission === false){
     $required_permission = 40;
    }

function xmlspecialchars($text){
     return str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES));
    }

function getBaseURL(){
     $pageURL = 'http';
     if ($_SERVER["HTTPS"] == "on"){
        $pageURL .= "s";
    }
     $pageURL .= "://";
     $dirname = str_replace("admin", "", dirname($_SERVER["REQUEST_URI"]));
     $dirname = str_replace("\\", "/", $dirname);
     $dirname = trim($dirname, "/");
     if($dirname != ""){
         $dirname = "/" . $dirname . "/";
         }else{
         $dirname = "/";
         }
     if ($_SERVER["SERVER_PORT"] != "80"){
         $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $dirname;
         }else{
         $pageURL .= $_SERVER["SERVER_NAME"] . $dirname;
         }
     return $pageURL;
    }


define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);


function generate_sitemap(){
    
     @set_time_limit(0);
     @ini_set('max_execution_time', 0);
    
     $xml_string = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
    
     $query_pages = mysql_query("SELECT * FROM " . tbname("content") . " WHERE active = 1 ORDER by lastmodified DESC");
     while($row = mysql_fetch_object($query_pages)){
         $xml_string .= "<url>\r\n";
         $xml_string .= "\t<loc>" . xmlspecialchars(getBaseURL() . $row -> systemname . ".html") . "</loc>\r\n";
         $xml_string .= "\t<lastmod>" . date("Y-m-d", $row -> lastmodified) . "</lastmod>\r\n";
         $xml_string .= "</url>\r\n\r\n";
        
         if(containsModule($row -> systemname, "blog")){
             $query_blog = mysql_query("SELECT * FROM " . tbname("blog") . " WHERE entry_enabled = 1 ORDER by datum DESC");
             while($row2 = mysql_fetch_object($query_blog)){
                 $xml_string .= "<url>\r\n";
                 $xml_string .= "\t<loc>" . xmlspecialchars(getBaseURL() . $row -> systemname . ".html?single=" . $row2 -> seo_shortname) . "</loc>\r\n";
                 $xml_string .= "\t<lastmod>" . date("Y-m-d", $row2 -> datum) . "</lastmod>\r\n";
                 $xml_string .= "</url>\r\n\r\n";
                 }
             }
         }
    
    
    
    
     $xml_string .= "</urlset>";
     $xml_string = str_replace("\r\n", "\n", $xml_string);
     $xml_string = str_replace("\r", "\n", $xml_string);
     $xml_string = str_replace("\n", "\r\n", $xml_string);
    
     $xml_file = "../sitemap.xml";
    
     $handle = @fopen($xml_file, "w");
     if($handle){
         fwrite($handle, $xml_string);
         fclose($handle);
        
         echo "<p><a href=\"../sitemap.xml\" target=\"_blank\">sitemap.xml</a> wurde generiert</p>";
         }else{
         echo "<p>sitemap.xml konnte nicht erzeugt werden.Bitte legen Sie die Datei manuell an und fügen Sie folgenden Code ein.</p>";
         echo "<textarea cols=70 rows=20>";
         echo htmlspecialchars($xml_string);
         echo "</textarea><br/><br/>";
         }
    }


// Konfiguration checken
$send_comments_via_email = getconfig("blog_send_comments_via_email") == "yes";

function xml_sitemap_admin(){
    
    if(isset($_POST["submit"]))
         generate_sitemap()
        
        
        
        ?>

<form action="<?php echo getModuleAdminSelfPath()?>" method="post">
<input type="submit" name="submit" value="Sitemap generieren"/>
</form>
<?php
        }
    
    ?>
