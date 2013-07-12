<?php
define("MODULE_ADMIN_HEADLINE", "Einstellungen des Blogmoduls");

$required_permission = getconfig("blog_required_permission");

if($required_permission === false){
     $required_permission = 50;
     }

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);

if(isset($_POST["submit"])){
    
     if($_POST["blog_send_comments_via_email"] == "yes"){
         setconfig("blog_send_comments_via_email", "yes");
         }else{
         setconfig("blog_send_comments_via_email", "no");
         }
    
     }

// Konfiguration checken
$send_comments_via_email = getconfig("blog_send_comments_via_email") == "yes";

function blog_admin(){
     ?>

<form action="<?php echo getModuleAdminSelfPath()?>" method="post">
<input type="checkbox" name="blog_send_comments_via_email" value="yes"
<?php if(getconfig("blog_send_comments_via_email") == "yes"){
         echo " checked";
         }
     ?>/> Über neue Kommentare per E-Mail benachrichtigen
<br/>
<br/>

<input type="submit" name="submit" value="Einstellungen speichern"/>
</form>
<?php
     }

?>