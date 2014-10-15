<?php

function language_selection(){
     $query = db_query("SELECT * FROM " . tbname("languages") . " ORDER by name");
     echo "<ul class='language_selection'>";
     while($row = db_fetch_object($query)){
         $domain = getDomainByLanguage($row -> language_code);
         if($domain)
             echo "<li>" . "<a href='http://" . $domain . "'>" . $row -> name . "</a></li>";
         else
             echo "<li>" . "<a href='./?language=" . $row -> language_code . "'>" . $row -> name . "</a></li>";
         }
     echo "</ul>";
    
     }

function get_category(){
     $current_page = get_page();
     return categories :: getCategoryById($current_page["category"]);
    }


function category(){
     echo get_category();
    }

function body_classes(){
     $str = "";
     if(is_frontpage()){
         $str .= "home ";
         }
    
     if(is_404()){
         $str .= "error404 ";
         }
    
     if(is_403()){
         $str .= "error403 ";
         }
    
     if(is_404() or is_403()){
         $str .= "errorPage ";
         }else{
         $str .= "page ";
         }
    
     if(containsModule(get_requested_pagename())){
         $str .= "containsModule ";
         }
    
     echo $str;
     }


// Gibt "Diese Seite läuft mit UliCMS" aus
function poweredByUliCMS(){
     echo "<p class=\"powered-by-ulicms\">Diese Seite läuft mit "
     . "<a href=\"http://www.ulicms.de\" target=\"_blnak\">UliCMS</a></p>";
     }

// Einen zufälligen Banner aus der Datenbank ausgeben
function random_banner(){
     $query = db_query("SELECT * FROM " . tbname("banner") . " ORDER BY RAND() LIMIT 1");
     if(db_num_rows($query) > 0){
         while($row = db_fetch_object($query)){
             $type = "gif";
             if(isset($row->type)){
                if(!empty($row->type)){
                   $type = $row->type;

                }

             }
             if($type == "gif"){
               $title = $row -> name;
               $link_url = $row -> link_url;
               $image_url = $row -> image_url;
               echo "<a href='$link_url' target='_blank'><img src='$image_url' title='$title' alt='$title' border=0></a>";
         } else if($type == "html"){
             echo $row->html;
         }
             }
        
         }
    
     }


function logo(){
     if(!getconfig("logo_image")){
         setconfig("logo_image", "");
         }
     if(!getconfig("logo_disabled")){
         setconfig("logo_disabled", "no");
         }
    
     $logo_path = "content/images/" . getconfig("logo_image");
    
     if(getconfig("logo_disabled") == "no" and file_exists($logo_path)){
         echo '<img class="website_logo" src="' . $logo_path . '" alt="' . htmlspecialchars(getconfig("homepage_title"),
             ENT_QUOTES, "UTF-8") . '"/>';
         }
    
     }


function year(){
     echo date("Y");
     }

function homepage_owner(){
     echo getconfig("homepage_owner");
     }


function homepage_title(){
     echo htmlspecialchars(getconfig("homepage_title"),
         ENT_QUOTES, "UTF-8");
     }



$status = check_status();

function meta_keywords($ipage = null){
     $status = check_status();
     $ipage = db_escape($_GET["seite"]);
     $query = db_query("SELECT * FROM " . tbname("content") . " WHERE systemname='$ipage'");
    
     if(db_num_rows($query) > 0){
         while($row = db_fetch_object($query)){
             if(!empty($row -> meta_keywords)){
                 return $row -> meta_keywords;
                 }
             }
         }
    
     return getconfig("meta_keywords");
     }

function meta_description($ipage = null){
     $status = check_status();
     $ipage = db_escape($_GET["seite"]);
     $query = db_query("SELECT * FROM " . tbname("content") . " WHERE systemname='$ipage'", $connection);
     if($ipage == ""){
         $query = db_query("SELECT * FROM " . tbname("content") . " ORDER BY id LIMIT 1", $connection);
         }
     if(db_num_rows($query) > 0){
         while($row = db_fetch_object($query)){
             if(!empty($row -> meta_description)){
                 return $row -> meta_description;
                 }
             }
        
        
         }
    
    
     return getconfig("meta_description");
     }


function get_title($ipage = null, $headline = false){
     $status = check_status();
     if($status == "404 Not Found"){
         return "Seite nicht gefunden";
         }else if($status == "403 Forbidden"){
         return "Zugriff verweigert";
         }
    
     $ipage = db_escape($_GET["seite"]);
     $query = db_query("SELECT * FROM " . tbname("content") . " WHERE systemname='$ipage'", $connection);
     if($ipage == ""){
         $query = db_query("SELECT * FROM " . tbname("content") . " ORDER BY id LIMIT 1");
         }
     if(db_num_rows($query) > 0){
         while($row = db_fetch_object($query)){
             if($headline and isset($row->alternate_title) and !empty($row->alternate_title)){
                $title = $row -> alternate_title;
             } else {
               $title = $row -> title;
             }

                $title = apply_filter($title, "title");
             return $title;
             }
         }
     }

function title($ipage = null){
     echo stringHelper :: real_htmlspecialchars(get_title($ipage));
}

function get_headline($ipage = null){
     return get_title($ipage, true);
}

function headline($ipage = null){
     echo stringHelper :: real_htmlspecialchars(get_headline($ipage));
}



function import($ipage){
     $ipage = db_escape($ipage);
     if($ipage == ""){
         $query = db_query("SELECT * FROM " . tbname("content") . " ORDER BY id LIMIT 1");
        
         }
    else{
         $query = db_query("SELECT * FROM " . tbname("content") . " WHERE systemname='$ipage'");
         }
    
     if(db_num_rows($query) == 0){
         return false;
         }else{
        
         while($row = db_fetch_object($query)){
             $row -> content = replaceShortcodesWithModules($row -> content);
             $row -> content = apply_filter($row -> content, "content");
             $row -> content = correctHTMLValidationErrors($row -> content);
            
             echo $row -> content;
             return true;
             }
        
         }
    
     }

// Todo: nicht W3-konformen HTML-Code korrigieren
function correctHTMLValidationErrors($txt){
     if(getconfig("disable_html_validation")){
         return $txt;
         }
    
     // Ersetze & durch &amp;
    $txt = preg_replace('/[&](?![A-Za-z]+[;])/', "&amp;", $txt);
    
     // replaced deprecated HTML-Tags
    $txt = str_ireplace("<center>", "<div style=\"text-align:center\">", $txt);
     $txt = str_ireplace("</center>", "</div>", $txt);
     $txt = str_ireplace("<strike>", "<del>", $txt);
     $txt = str_ireplace("</strike>", "</del>", $txt);
     $txt = str_ireplace("<s>", "<del>", $txt);
     $txt = str_ireplace("</s>", "</del>", $txt);
     $txt = str_ireplace("<tt>", "<code>", $txt);
     $txt = str_ireplace("</tt>", "</code>", $txt);
     $txt = str_ireplace("<dir>", "<ul>", $txt);
     $txt = str_ireplace("</dir>", "</ul>", $txt);
     $txt = str_ireplace("<acronym>", "<abbr>", $txt);
     $txt = str_ireplace("</acronym>", "</abbr>", $txt);
    
     return $txt;
     }

function apply_filter($text, $type){
     $modules = getAllModules();
     for($i = 0; $i < count($modules); $i++){
         $module_content_filter_file = getModulePath($modules[$i]) .
         $modules[$i] . "_" . $type . "_filter.php";
         if(file_exists($module_content_filter_file)){
             include_once $module_content_filter_file;
             if(function_exists($modules[$i] . "_" . $type . "_filter")){
                 $text = call_user_func($modules[$i] . "_" . $type . "_filter",
                     $text);
                 }
            
             }
        
        
         }
    
     return $text;
    
    
     }


function motto(){
     echo htmlspecialchars(getconfig("motto"), ENT_QUOTES, "UTF-8");
     }


function get_frontpage(){
     setLanguageByDomain();
    
     if(isset($_SESSION["language"])){
         $frontpage = getconfig("frontpage_" . $_SESSION["language"]);
        
         if($frontpage){
             return $frontpage;
             }
        
         }
    
    
    
     return getconfig("frontpage");
    
    }


function get_requested_pagename(){
     $value = db_escape($_GET["seite"]);
     if($value == ""){
         $value = get_frontpage();
         }
     return $value;
     }

function is_frontpage(){
     return get_requested_pagename() === get_frontpage();
     }

function is_200(){
     return check_status() == "200 OK";
     }

function is_404(){
     return check_status() == "404 Not Found";
     }

function is_403(){
     return check_status() == "403 Forbidden";
     }

function menu($name){
     $language = $_SESSION["language"];
     $query = db_query("SELECT * FROM " . tbname("content") . " WHERE menu='$name' AND language = '$language' AND active = 1 AND `deleted_at` IS NULL AND parent IS NULL ORDER by position");
     echo "<ul class='menu_" . $name . "'>\n";
     while($row = db_fetch_object($query)){
         echo "  <li>" ;
         if(get_requested_pagename() != $row -> systemname){
             echo "<a href='" . buildSEOUrl($row -> systemname) . "' target='" .
             $row -> target . "'>";
             }else{
             echo "<a class='menu_active_link' href='" . buildSEOUrl($row -> systemname) . "' target='" . $row -> target . "'>";
             }
        
         echo htmlentities($row -> title, ENT_QUOTES, "UTF-8");
         echo "</a>\n";
        
         // Unterebene 1
        $query2 = db_query("SELECT * FROM " . tbname("content") . " WHERE active = 1 AND language = '$language' AND `deleted_at` IS NULL AND parent=" . $row -> id . " ORDER by position");
        
         if(db_num_rows($query2) > 0){
             echo "<ul class='sub_menu'>\n";
             while($row2 = db_fetch_object($query2)){
                
                 echo "      <li>";
                 if(get_requested_pagename() != $row2 -> systemname){
                     echo "<a href='" . buildSEOUrl($row2 -> systemname) . "' target='" .
                     $row -> target . "'>";
                     }else{
                     echo "<a class='menu_active_link' href='" . buildSEOUrl($row2 -> systemname) . "' target='" .
                     $row -> target . "'>";
                     }
                 echo htmlentities($row2 -> title, ENT_QUOTES, "UTF-8");
                 echo '</a>';
                
                 // Unterebene 2
                $query3 = db_query("SELECT * FROM " . tbname("content") . " WHERE active = 1 AND language = '$language' AND parent=" . $row2 -> id . " AND `deleted_at` IS NULL ORDER by position");
                 if(db_num_rows($query3) > 0){
                     echo "  <ul class='sub_menu'>\n";
                     while($row3 = db_fetch_object($query3)){
                         echo "      <li>";
                         if(get_requested_pagename() != $row3 -> systemname){
                             echo "<a href='" . buildSEOUrl($row3 -> systemname) . "' target='" .
                             $row3 -> target . "'>";
                             }else{
                             echo "<a class='menu_active_link' href='" . buildSEOUrl($row3 -> systemname) . "' target='" .
                             $row3 -> target . "'>";
                             }
                         echo htmlentities($row3 -> title, ENT_QUOTES, "UTF-8");
                         echo '</a>';
                        
                         // Unterebene 3
                        $query4 = db_query("SELECT * FROM " . tbname("content") . " WHERE active = 1 AND `deleted_at` IS NULL AND language = '$language' AND parent=" . $row3 -> id . " ORDER by position");
                         if(db_num_rows($query4) > 0){
                             echo "  <ul class='sub_menu'>\n";
                             while($row4 = db_fetch_object($query4)){
                                 echo "<li>";
                                 if(get_requested_pagename() != $row4 -> systemname){
                                     echo buildSEOUrl($row4 -> systemname) . "' target='" .
                                     $row4 -> target . "'>";
                                     }else{
                                     echo "<a class='menu_active_link' href='" . buildSEOUrl($row4 -> systemname) . "' target='" .
                                     $row4 -> target . "'>";
                                     }
                                 echo htmlentities($row4 -> title, ENT_QUOTES, "UTF-8");
                                 echo '</a>';
                                 echo "</li>\n";
                                 }
                             echo "  </ul></li>\n";
                             }
                        
                         }
                     echo "  </ul></li>\n";
                     }else{
                     echo "</li>\n";
                     }
                
                 }
             echo "  </ul></li>\n";
             }else{
             echo "</li>\n";
             }
         }
    
     echo "</ul>\n";
     }




function base_metas(){
    
     $title_format = getconfig("title_format");
     if($title_format){
         $title = $title_format;
         $title = str_ireplace("%homepage_title%",
             getconfig("homepage_title"), $title);
         $title = str_ireplace("%title%", get_title(), $title);
        
         $title = htmlentities($title, ENT_QUOTES, "UTF-8");
        
         echo "<title>" . $title . "</title>\r\n";
        
         }
    
     $dir = dirname($_SERVER["SCRIPT_NAME"]);
     $dir = str_replace("\\", "/", $dir);
    
     if(endsWith($dir, "/") == false){
         $dir .= "/";
         }
    
     $robots = getconfig("robots");
     if($robots){
         $robots = apply_filter($robots, "meta_robots");
         echo '<meta name="robots" content="' . $robots . '"/>';
         echo "\r\n";
         }
    
    
     if(!getconfig("hide_meta_generator")){
         echo '<meta name="generator" content="UliCMS Release ' . cms_version()
         . '" />';
         echo "\r\n";
        
        
        
         $facebook_id = getconfig("facebook_id");
        
         if(!empty($facebook_id)){
             echo '<meta property="fb:admins" content="' . $facebook_id . '"/>';
             echo "\r\n";
             }
        
         }
    
     echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>';
     echo "\r\n";
    
    
     $keywords = meta_keywords();
     if(!$keywords){
         $keywords = getconfig("meta_keywords");
         }
     if($keywords != "" && $keywords != false){
        
        
         if(!getconfig("hide_meta_keywords")){
             $keywords = apply_filter($keywords, "meta_keywords");
             $keywords = htmlentities($keywords, ENT_QUOTES, "UTF-8");
             echo '<meta name="keywords" content="' . $keywords . '"/>';
             echo "\r\n";
             }
         }
     $description = meta_description();
     if(!$description){
         $description = getconfig("meta_description");
         }
     if($description != "" && $description != false){
        
         $description = apply_filter($description, "meta_description");
        
         $$description = htmlentities($description, ENT_QUOTES, "UTF-8");
         if(!getconfig("hide_meta_description")){
             echo '<meta name="description" content="' . $description . '"/>';
             echo "\r\n";
             }
         }
    
    
    
    
     echo '<link rel="stylesheet" type="text/css" href="core.css"/>';
     echo "\r\n";
    
     add_hook("head");
    
     $zoom = getconfig("zoom");
     if($zoom === false){
         setconfig("zoom", 100);
         $zoom = 100;
         }
    
     if(!getconfig("disable_custom_layout_options")){
         echo "
<style type=\"text/css\">
body{
zoom:" . $zoom . "%;
font-family:" . getconfig("default-font") . ";
font-size:" . getconfig("font-size") . "pt;
background-color:" . getconfig("body-background-color") . ";
color:" . getconfig("body-text-color") . ";
}
</style>";
         }
    
    
     }



function head(){
     base_metas();
     }

function autor(){
     $seite = $_GET["seite"];
     if(empty($seite)){
         $query = db_query("SELECT * FROM " . tbname("content") . " ORDER BY id LIMIT 1");
         $result = db_fetch_object($query);
         $seite = $result -> systemname;
         }
    
     if(check_status() != "200 OK"){
         return;
         }
    
     $query = db_query("SELECT * FROM " . tbname("content") . " WHERE systemname='" . db_escape($seite) . "'", $connection);
     if(db_num_rows($query) < 1){
         return;
         }
     $result = db_fetch_array($query);
     if($result["systemname"] == "kontakt" || $result["systemname"] == "impressum" || StartsWith($result["systemname"], "menu_")){
         return;
         }
     $query2 = db_query("SELECT * FROM " . tbname("users") . " WHERE id=" . $result["autor"], $connection);
     $result2 = db_fetch_array($query2);
     if(db_num_rows($query2) == 0){
         return;
         }
     $datum = date(getconfig("date_format"), $result["created"]);
     $out = getconfig("autor_text");
     $out = str_replace("Vorname", $result2["firstname"], $out);
     $out = str_replace("Nachname", $result2["lastname"], $out);
     $out = str_replace("Username", $result2["username"], $out);
     $out = str_replace("Datum", $result2["datum"], $out);
     if(!is_403() or $_SESSION["group"] >= 20){
         echo $out;
         }
     }


function get_page($systemname = ""){
     if(empty($systemname)){
         $systemname = $_GET["seite"];
         }
    
     if(empty($systemname))
         $systemname = get_frontpage();
    
    
     $query = db_query("SELECT * FROM " . tbname("content") . " WHERE systemname='" . db_escape($systemname) . "'");
     return db_fetch_assoc($query);
    }

function content(){
     $theme = getconfig("theme");
     $status = check_status();
     if($status == "404 Not Found"){
         if(file_exists(getTemplateDirPath($theme) . "404.php"))
             include getTemplateDirPath($theme) . "404.php";
         else
             echo "Die von Ihnen gew&uuml;nschte Seite existiert nicht.";
         return false;
         }else if($status == "403 Forbidden"){
         if(file_exists(getTemplateDirPath($theme) . "403.php"))
             include getTemplateDirPath($theme) . "403.php";
         else
             echo "Sie verfügen nicht über die erforderlichen Rechte um auf diese Seite zugreifen zu können.";
         return false;
         }
    
    
     if(!is_logged_in())
         db_query("UPDATE " . tbname("content") . " SET views = views + 1 WHERE systemname='" . $_GET["seite"] . "'");
     return import($_GET["seite"]);
     }


function check_status(){
     if($_GET["seite"] == ""){
         $_GET["seite"] = get_frontpage();
         }
    
     $page = $_GET["seite"];
     $cached_page_path = buildCacheFilePath($page);
    
     if(file_exists($cached_page_path)){
         $last_modified = filemtime($cached_page_path);
         if(time() - $last_modified < CACHE_PERIOD){
             return "200 OK";
             }
         }
    
     $test = db_query("SELECT * FROM `" . tbname("content") . "` WHERE systemname='" . db_escape($_GET["seite"]) . "'");
     if(db_num_rows($test) == 0){
         return "404 Not Found";
         }else{
         $test_array = db_fetch_array($test);
        
         // Prüfe, ob der Nutzer die Berechtigung zum Zugriff auf die Seite hat.
        if($test_array["active"] == 1 or $_SESSION["group"] >= 20){
            
             $access = explode(",", $test_array["access"]);
            
             $permitted = false;
            
             if(in_array("all", $access)){
                 $permitted = true;
                 }
             if(in_array("admin", $access) and $_SESSION["group"] >= 50){
                 $permitted = true;
                 }
            
             if(in_array("registered", $access) and $_SESSION["group"] >= 10){
                 $permitted = true;
                 }
            
             if($permitted){
                
                 if($test_array["redirection"] != ""){
                     header("Location: " . $test_array["redirection"]);
                     exit();
                     }
                 if($test_array["deleted_at"] != null){
                     return "404 Not Found";
                     }
                 return "200 OK";
                
                 }
            else{
                 return "403 Forbidden";
                 }
             }
        else{
             return "403 Forbidden";
             }
         }
     }
