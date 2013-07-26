<?php 
define("MODULE_ADMIN_HEADLINE", "Einstellungen des Blogmoduls");

$required_permission = getconfig("blog_required_permission");

if($required_permission === false){
   $required_permission = 50;
}

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);


function blog_admin(){

if(isset($_POST["submit"])){

   if($_POST["blog_send_comments_via_email"] == "yes"){
      setconfig("blog_send_comments_via_email", "yes");
   } else{
      setconfig("blog_send_comments_via_email", "no");
   }
   if(intval($_POST["blog_feed_max_items"])> 0){
      setconfig("blog_feed_max_items", intval($_POST["blog_feed_max_items"]));
   }

}

// Konfiguration checken
$send_comments_via_email = getconfig("blog_send_comments_via_email") == "yes";

$blog_feed_max_items = getconfig("blog_feed_max_items");
if($blog_feed_max_items === false){
   setconfig("blog_feed_max_items", "10");
   $blog_feed_max_items = "10";
}

?>

<form action="<?php echo getModuleAdminSelfPath()?>" method="post">
<p><input type="checkbox" name="blog_send_comments_via_email" value="yes"
<?php if(getconfig("blog_send_comments_via_email") == "yes"){
echo " checked";
}?>/> Über neue Kommentare per E-Mail benachrichtigen</p>
<p><input type="number" name="blog_feed_max_items" size=3 maxlength=3 min="5" max="100" value="<?php echo $blog_feed_max_items;?>"/> maximale Anzahl der Einträge im Newsfeed
</p>

<p><input type="submit" name="submit" value="Einstellungen speichern"/></p>
</form>
<?php
}
 
?>