<?php
if(!defined("ULICMS_ROOT"))
     die("Schlechter Hacker");

 $acl = new ACL();
?>
<h1><?php echo TRANSLATION_SETTINGS;
?></h1>
<?php if($acl -> hasPermission("settings_simple") or $acl -> hasPermission("design") or
         $acl -> hasPermission("spam_filter") or $acl -> hasPermission("cache") or
         $acl -> hasPermission("motd") or $acl -> hasPermission("pkg_settings") or
         $acl -> hasPermission("logo") or $acl -> hasPermission("languages") or
         $acl -> hasPermission("other")){
     ?>
<p>
<?php if($acl -> hasPermission("settings_simple")){
         ?>
<a href="index.php?action=settings_simple"><?php echo TRANSLATION_GENERAL_SETTINGS;
         ?></a>
<br/>
<?php }
     ?>

<?php if($acl -> hasPermission("design")){
         ?>
<a href="index.php?action=design"><?php echo TRANSLATION_DESIGN;
         ?></a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("spam_filter")){
         ?>
<a href="index.php?action=spam_filter"><?php echo TRANSLATION_SPAMFILTER;
         ?></a>

<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("cache")){
         ?>
<a href="index.php?action=cache&clear_cache=yes"><?php echo TRANSLATION_CLEAR_CACHE;
         ?></a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("motd")){
         ?>
<a href="index.php?action=motd"><?php echo TRANSLATION_MOTD;
         ?></a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("pkg_settings")){
         ?>
<a href="?action=pkg_settings"><?php echo TRANSLATION_PACKAGE_SOURCE;
         ?></a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("logo")){
         ?>
<a href="index.php?action=logo_upload"><?php echo TRANSLATION_LOGO;
         ?></a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("languages")){
         ?>
<a href="index.php?action=languages"><?php echo TRANSLATION_LANGUAGES;
         ?></a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("other")){
         ?>
<a href="?action=other_settings"><?php echo TRANSLATION_OTHER;
         ?></a>
<?php }
     ?>
</p>
<?php
     }else{
     noperms();
     }

?>
