<?php
if(!defined("ULICMS_ROOT"))
     die("Bad Hacker!");

$acl = new ACL();

if($acl -> hasPermission("images") or $acl -> hasPermission("flash") or $acl -> hasPermission("files")){
    
    
     ?>

<h2><?php echo TRANSLATION_MEDIA;
     ?></h2>
<strong><?php echo ULICMS_PLEASE_SELECT_FILETYPE;
     ?></strong><br/>
<?php if($acl -> hasPermission("images")){
         ?>
<a href="index.php?action=images"><?php echo TRANSLATION_IMAGES;
         ?></a><br/>
<?php }
     ?>
<?php if($acl -> hasPermission("flash")){
         ?>
<a href="index.php?action=flash"><?php echo TRANSLATION_FLASH;
         ?></a><br/>
<?php }
     ?>
<?php if($acl -> hasPermission("files")){
         ?>
<a href="index.php?action=files"><?php echo TRANSLATION_FILES;
         ?></a><br/>
<?php }
     ?>


<?php if($acl -> hasPermission("videos")){
         ?>
<a href="index.php?action=videos"><?php echo TRANSLATION_VIDEOS;
         ?></a><br/>
<?php }
     ?>




<?php }else{
     noperms();
    
     }
?>
