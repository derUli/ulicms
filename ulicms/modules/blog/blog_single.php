<?php
if(file_exists("antispam-features.php")){
     include "antispam-features.php";
     }else{
    
     function isCountryBlocked(){
         return false;
         }
    
     }



function blog_single($seo_shortname){
    
     $autor_and_date = getconfig("blog_autor_and_date_text");
    
     $query = db_query("SELECT * FROM `" . tbname("blog") . "` WHERE seo_shortname='$seo_shortname'");
    
     // count views is user not logged in
    if(!is_logged_in())
         db_query("UPDATE `" . tbname("blog") . "` SET views = views + 1
       WHERE seo_shortname='$seo_shortname'");
    
    
    
     if(mysql_num_rows($query) > 0){
         $post = mysql_fetch_object($query);
         $user = getUserById($post -> author);
        
         $html = "";
        
         if($_SESSION["group"] >= 20 or $post -> entry_enabled){
            
             $html .= "<h1 class='blog_headline'><a name='headline' href='#headline'>" . $post -> title . "</a></h1>";
             $html .= "<hr class='blog_hr'/>";
            
             $date_and_autor_string = $autor_and_date;
            
             $date_and_autor_string = str_replace("%date%",
                 date(getconfig("date_format"), $post -> datum),
                 $date_and_autor_string);
            
             $date_and_autor_string = str_replace("%username%",
                 $user["username"],
                 $date_and_autor_string);
            
             $date_and_autor_string = str_replace("%firstname%",
                 $user["firstname"],
                 $date_and_autor_string);
             $date_and_autor_string = str_replace("%lastname%",
                 $user["lastname"],
                 $date_and_autor_string);
            
             $date_and_autor_string = str_replace("%views%", $post -> views, $date_and_autor_string);
            
             $html .= $date_and_autor_string;
            
             $html .= "<br/>";
             $html .= "<div class='blog_post_content'>" . $post -> content_full . "</div>";
            
             $html .= "<br/>";
            
             if(($_SESSION["group"] >= 20 and $_SESSION["login_id"] == $post -> author)
                     or ($_SESSION["group"] >= 40)){
                 $html .= "<a href='" . get_requested_pagename() . ".html?blog_admin=edit_post&id=" . $post -> id . "'>[Bearbeiten]</a> ";
                
                 $html .= "<a href='" . get_requested_pagename() . ".html?blog_admin=delete_post&id=" . $post -> id . "' onclick='return confirm(\"Diesen Post wirklich löschen?\")'>[Löschen]</a>";
                 }else if($_SESSION["group"] >= 20){
                 $html .= "
		   <div class='disabled_link'>[Bearbeiten]</div>
		   <div class='disabled_link'>[Löschen]</div>";
                 }
            
            
            
             if($post -> comments_enabled){
                 $html .= "" .
                 blog_display_comments($post -> id);
                 }
            
            
             return $html;
            
            
            
             }else{
             return "<p class='ulicms_error'>Dieser Blogartikel ist momentan deaktiviert.</p>";
             }
         }else{
        
         return "<p class='ulicms_error'>Dieser Blogartikel existiert nicht mehr.<br/>
       Vielleicht bist du einem toten Link gefolgt?</p>";
        
        
         }
    
     }

function comment_form($post_id){
     $html = "<div class=\"comment_form\">";
     $html .= "<form name='form' action='" . $_SERVER['REQUEST_URI'] . "' method='post'>";
     if($_SESSION["language"] == "de"){
         $submit = "Kommentar veröffentlichen";
         }else{
         $submit = "Submit Comment";
         }
    
    
     $html .= "<table border=0>
     <tr>
     <td><strong>Name: *</strong>&nbsp;&nbsp;</td><td><input name='name' size=50 maxlength=255 type='text' value='" . $_SESSION["name"] . "'>
     </td>
     </tr>";
    
    
     $html .= "<tr>
     <td><strong>Homepage:</strong>&nbsp;&nbsp;</td><td><input size=50 maxlength=255 name='url' type='text' value='" . $_SESSION["url"] . "'>
     </td>
     </tr>";
    
     $html .= "<tr>
     <td><strong>Email: *</strong>&nbsp;&nbsp;</td><td><input size=50 maxlength=255 name='email' type='text' value='" . $_SESSION["email"] . "'>
     </td>
     </tr>";
    
    
     $html .= "</table>";
    
     $html .= "<br/><textarea name='comment' rows=15 cols=60></textarea>";
     $html .= "<input type='text' name='phone' class='antispam_honeypot' value=''>";
     $html .= "<input type='hidden' name='post_comment_to' value='" . $post_id . "'>";
     $html .= "<br/><br/><div class=\"ulicms_publish_comment\"><input type='submit' value='" . $submit . "'></div>";
     $html .= "</form></div>";
    
     return $html;
     }


function send_comment_via_email($article_title, $article_url, $name, $txt){
     $txt = str_replace("\\r\\n", "\n", $txt);
    
     $subject = "Neuer Kommentar zum Artikel \"" . $article_title . "\"";
     $message = "$name hat einen neuen Kommentar zum Artikel \"$article_title\" geschrieben.\n\n" .
     "Kommentar:\n" . $txt . "\n\n" .
     "Klicke hier, um den Kommentar aufzurufen:\n" .
     $article_url;
     $header = "From: " . getconfig("email") . "\n" .
     "Content-type: text/plain; charset=utf-8";
     @mail(getconfig("email"),
         $subject, $message, $header);
     }

function post_comments(){
     if(!isset($_SESSION["name"])){
         if(isset($_SESSION["login_id"])){
             $user = getUserById($_SESSION["login_id"]);
             $_SESSION["name"] = $user["username"];
             $_SESSION["email"] = $user["email"];
             }
        
         }
    
    
    
     if(!isset($_SESSION["url"])){
         $_SESSION["url"] = "http://";
         }
    
    
    
    
     if(isset($_POST["post_comment_to"])){
         $post_id = intval($_POST["post_comment_to"]);
         $name = mysql_real_escape_string(htmlspecialchars($_POST["name"]));
         $url = mysql_real_escape_string(htmlspecialchars($_POST["url"]));
         $email = mysql_real_escape_string(htmlspecialchars(
                $_POST["email"]));
         $date = time();
         $comment_unescaped = $_POST["comment"];
         $comment = mysql_real_escape_string($_POST["comment"]);
        
         $_SESSION["name"] = $name;
         $_SESSION["url"] = $url;
         $_SESSION["email"] = $email;
        
         // wenn Spamfilter aktiviert ist
        if(getconfig("spamfilter_enabled") == "yes"){
            
             // Spam Protection
            // ein für echte Menschen unsichtbares Textfeld
            // Die meisten Spambots füllen alle Felder aus
            // dieses Feld wird darauf geprüft, ob es nicht leer ist
            if(!empty($_POST["phone"])){
                 echo("Die motherfucking spammers!");
                 return false;
                 }
            
            
             if(stringcontainsbadwords($_POST["name"]) or
                     stringcontainsbadwords($_POST["comment"])){
                 return false;
                 }
            
             }
        
        
         if(!empty($name) and !empty($email) and !empty($comment)){
             db_query("INSERT INTO `" . tbname("blog_comments") . "` 
	(name, url, email, date, comment, post_id)
	VALUES ( '$name', '$url', '$email', $date, '$comment', $post_id);");
             $comment_id = mysql_insert_id();
            
             if(getconfig("blog_send_comments_via_email") == "yes"){
                 $query = db_query("SELECT * FROM " . tbname("blog") . " WHERE id = $post_id");
                 $post = mysql_fetch_object($query);
                 $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                 $domainName = $_SERVER['HTTP_HOST'];
                 $article_url = $protocol . $domainName . getModuleAdminSelfPath() .
                 "#comment" . $comment_id;
                 send_comment_via_email($post -> title,
                     $article_url, $name, $comment_unescaped);
                
                 }
            
             return true;
             }else{
             return false;
             }
        
         }
     }


function stringcontainsbadwords($str){
     $words_blacklist = getconfig("spamfilter_words_blacklist");
     $str = strtolower($str);
    
     if($words_blacklist !== false){
         $words_blacklist = explode("||", $words_blacklist);
         }
    else{
         return false;
         }
    
     for($i = 0; $i < count($words_blacklist); $i++){
         $word = strtolower($words_blacklist[$i]);
         if(strpos($str, $word) !== false)
             return true;
         }
    
    
     return false;
     }

function blog_display_comments($post_id){
     $html = "";
    
    
     $spamfilter_enabled = getconfig("spamfilter_enabled") == "yes";
    
     if(isCountryBlocked() and $spamfilter_enabled){
         if($_SESSION["language"] == "de"){
             $html .= "<p class='ulicms_error'>
           Benutzer aus Ihrem Land werden vom Spamfilter blockiert.<br/> Wenn Sie denken, dass das ein Fehler ist, 
           wenden Sie sich bitte an den Administrator dieser Internetseite.</p>";
             }
        else{
             $html .= "<p class='ulicms_error'>Users from your Country are blocked by the spamfilter. If you believe, this is an error, please contact the administrator.</p>";
             }
         }
    
    else if($spamfilter_enabled and (stringcontainsbadwords($_POST["name"]) or
                 stringcontainsbadwords($_POST["comment"]))){
         if($_SESSION["language"] == "de"){
             $html .= "<p class='ulicms_error'>" .
             "Ihr Kommentar enthält nicht erlaubte Wörter.</p>";
             }
        else{
             $html .= "<p class='ulicms_error'>" .
             "Your comment contains not allowed words.</p>";
             }
         }
    
    
    
    
    else{
        
         if(post_comments($post -> id)){
             $html .= "<script type='text/javascript'>
	    location.replace(location.href);
	    </script>";
            
             }
         }
    
    
    
     $query = db_query("SELECT * FROM `" . tbname("blog_comments") . "` WHERE post_id = $post_id");
    
     $html .= "<div class='comments'>";
     if($_SESSION["language"] == "de"){
         $html .= "<h2>Kommentare</h2>";
         }
    else{
         $html .= "<h2>Comments</h2>";
         }
     $html .= comment_form($post_id);
    
     if(mysql_num_rows($query) > 0){
        
         $count = 0;
        
         if($_SESSION["language"] == "de"){
             $html .= "<p>Es sind bisher " . mysql_num_rows($query) .
             " Kommentare zu diesem Artikel vorhanden.</p>";
             }else{
             $html .= "<p>There are " . mysql_num_rows($query) . " Comments
	 until now.</p>";
             }
        
         $html .= "<hr/>";
        
         while($comment = mysql_fetch_object($query)){
             $count++;
            
             $html .= "<div class='a_comment'>
	   <a href='#comment" . $comment -> id . "' name='comment" . $comment -> id . "'>";
             $html .= "#" . $count;
            
            
            
            
             $html .= "</a>";
            
             if($_SESSION["group"] >= 40){
                 $html .= " <a href='" . get_requested_pagename() . ".html?blog_admin=delete_comment&id=" . $comment -> id . "' onclick='return confirm(\"Diesen Kommentar wirklich löschen?\")'>[Löschen]</a>";
                 }
            
             $html .= "<br/>";
             $html .= "<br/>";
             $html .= "<strong>Name: </strong>";
             $html .= $comment -> name;
             $html .= "<br/>";
            
             if($_SESSION["group"] >= 20){
                 $html .= "<strong>Email: </strong>" . $comment -> email . "<br/>";
                 }
            
             if($_SESSION["language"] == "de"){
                 $html .= "<strong>Datum:</strong>";
                
                 }else{
                 $html .= "<strong>Date:</strong>";
                 }
            
            
            
            
             $html .= " ";
             $html .= date(getconfig("date_format"),
                 $comment -> date);
             if($comment -> url != "http://" and $comment -> url != ""){
                 $html .= "<br/>";
                 $html .= "<strong>Homepage:</strong> " . "<a href='" . $comment -> url . "' target='_blank' rel='nofollow'>" . $comment -> url . "</a>";
                 }
             $html .= "<br/><br/>";
             $html .= nl2br(htmlspecialchars($comment -> comment));
            
             $html .= "<br/><br/>";
            
             if($count != mysql_num_rows($query)){
                 $html .= "<hr/>";
                 }
            
            
             $html .= "</div>";
             }
        
        
        
         }else{
         if($_SESSION["language"] == "de"){
             $html .= "<p>Es sind bisher noch keine Kommentare zu diesem Artikel vorhanden.</p>";
             }
        else{
             $html .= "<p>No Comments existing yet.</p>";
             }
         }
    
     $html .= "</div>";
    
     return $html;
     }
?>
