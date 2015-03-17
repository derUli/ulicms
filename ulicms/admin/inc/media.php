<?php
if(!defined("ULICMS_ROOT"))
     die("Bad Hacker!");

$acl = new ACL();

if($acl -> hasPermission("images") or $acl -> hasPermission("videos") or $acl -> hasPermission("audio") or $acl -> hasPermission("files")){
    
    
     ?>

<h2><?php echo TRANSLATION_MEDIA;
     ?></h2>
<strong><?php echo ULICMS_PLEASE_SELECT_FILETYPE;
     ?></strong><br/><br/>
<?php if($acl -> hasPermission("images")){
         ?>
<a href="index.php?action=images"><?php echo TRANSLATION_IMAGES;
         ?></a><br/><br/>
<?php }
     ?>
<?php if($acl -> hasPermission("files")){
         ?>
<a href="index.php?action=files"><?php echo TRANSLATION_FILES;
         ?></a><br/><br/>
<?php }
     ?>


<?php if($acl -> hasPermission("videos")){
         ?>
<a href="index.php?action=videos"><?php echo TRANSLATION_VIDEOS;
         ?></a><br/><br/>
<?php }
     ?>
     
     <?php if($acl -> hasPermission("audio")){
         ?>
<a href="index.php?action=audio"><?php echo TRANSLATION_AUDIO;
         ?></a>
<?php }
     ?>




<?php }else{
     noperms();
    
     }
?>
