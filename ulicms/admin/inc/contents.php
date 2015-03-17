<?php
if(!defined("ULICMS_ROOT"))
     die("schlechter Hacker");

$acl = new ACL();

if($acl -> hasPermission("pages") or $acl -> hasPermission("banners") or $acl -> hasPermission("categories") or $acl -> hasPermission("export")){
     ?>

<h2><?php echo TRANSLATION_CONTENTS;
     ?></h2>
<p><strong><?php echo TRANSLATION_SELECT_CONTENT_TYPE;
     ?></strong><br/><br/>
<?php if($acl -> hasPermission("pages")){
         ?>
<a href="index.php?action=pages"><?php echo TRANSLATION_PAGES;
         ?></a><br/><br/>
<?php }
     ?>
<?php if($acl -> hasPermission("banners")){
         ?>
<a href="index.php?action=banner"><?php echo TRANSLATION_ADVERTISEMENTS;
         ?></a><br/><br/>
<?php }
     ?>
    
 
<?php if($acl -> hasPermission("categories")){
         ?>

<a href="index.php?action=categories"><?php echo TRANSLATION_CATEGORIES;
         ?></a><br/><br/>
<?php }
     ?>

<?php
     if($acl -> hasPermission("export") or $acl -> hasPermission("import")){
         ?>

<p><strong><?php echo TRANSLATION_IMPORT_EXPORT;
         ?></strong><br/><br/>
<?php }
     ?>

<?php if($acl -> hasPermission("import")){
         ?>
<!--
<a href="index.php?action=import"><?php echo TRANSLATION_IMPORT;
         ?></a><br/><br/>

-->
<?php }
     ?>

<?php if($acl -> hasPermission("export")){
         ?>

<a href="index.php?action=export"><?php echo TRANSLATION_EXPORT;
         ?></a><br/>
<?php }
     ?>

</p>
<?php add_hook("content_type_list_entry");
     ?>






<?php
     }else{
     noperms();
     }
?>
