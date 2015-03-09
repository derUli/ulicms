<?php

if(defined("_SECURITY")){
     $acl = new ACL();
     if(is_admin() or $acl -> hasPermission("update_system")){
     $patches = file_get_contents_wrapper(PATCH_CHECK_URL, true);
     if(!$patches or empty($patches)){
        echo "<p class='ulicms_error'>".get_translation("no_patches_available")."</p>";
     } else {
     ?>
<form action="index.php?action=install_patches" method="post">

<input type="submit" value="<?php translate("install_selected_patches");?>"
</form>
<?php

}


 } else {
    noperms();
}


}
?>
