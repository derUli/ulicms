<?php
if(defined("_SECURITY")){
     $acl = new ACL();
     if(is_admin() or $acl -> hasPermission("update_system")){
         ?>

<?php if(file_exists("../update.php")){
             ?>
	<p><a href="../update.php"><?php echo TRANSLATION_RUN_UPDATE;
            ?></a></p>
	<?php echo TRANSLATION_UPDATE_NOTICE;
            ?>
<?php
             }else{
             ?>
<?php echo TRANSLATION_UPDATE_INFORMATION_TEXT;
            ?>
	<p>

<?php }
         ?>

<?php
        
         }else{
         noperms();
         }
    
    
     }
?>