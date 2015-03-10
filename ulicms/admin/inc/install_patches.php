<?php
if(defined("_SECURITY")){
     $acl = new ACL();
     if(is_admin() or $acl -> hasPermission("update_system")){

$patches = $_POST["patches"];
$pkg = new PackageManager();

foreach($patches as $patch){
   $splitted = explode("|", $patch);
   $success = $pkg->installPatch($splitted[0], $splitted[1], $splitted[2]);
   if($success){
      echo '<p style="color:green">'.htmlspecialchars($splitted[0])." ".get_translation("was_successfully_installed").'</p>';
   } else {
     echo '<p style="color:red">'.get_translation("installation_of")." ".htmlspecialchars($splitted[0])." ".get_Translation("is_failed")."</p>";
}

}

} else {
noperms();
}

}
