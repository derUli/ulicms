<?php 
define("MODULE_ADMIN_HEADLINE", "Newsletter versenden");

$required_permission = getconfig("newsletter_required_permission");

if($required_permission === false){
   $required_permission = 20;
}

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);

include getModulePath("newsletter")."newsletter_install.php";
newsletter_check_install();


function newsletter_admin(){

if(!isset($_GET["newsletter_action"])){
?>
<a href="<?php echo getModuleAdminSelfPath()?>&newsletter_action=send_newsletter">Newsletter senden</a>
<br/>
<a href="<?php echo getModuleAdminSelfPath()?>&newsletter_action=show_subscribers">Abonnenten anzeigen</a>
<br/>
<?php

}
}
 
?>