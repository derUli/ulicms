<?php
if(!defined("ULICMS_ROOT"))
     die("Schlechter Hacker");

 $acl = new ACL();
?>
<h1>Einstellungen</h1>
<?php if($acl -> hasPermission("settings_simple") or $acl -> hasPermission("design") or
         $acl -> hasPermission("spam_filter") or $acl -> hasPermission("cache") or
         $acl -> hasPermission("motd") or $acl -> hasPermission("pkg_settings") or
         $acl -> hasPermission("logo") or $acl -> hasPermission("languages") or
         $acl -> hasPermission("other")){
     ?>
<p>
<?php if($acl -> hasPermission("settings_simple")){
         ?>
<a href="index.php?action=settings_simple">Grundeinstellungen</a>
<br/>
<?php }
     ?>

<?php if($acl -> hasPermission("design")){
         ?>
<a href="index.php?action=design">Design</a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("spam_filter")){
         ?>
<a href="index.php?action=spam_filter">Spamfilter</a>

<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("cache")){
         ?>
<a href="index.php?action=cache">Cache</a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("motd")){
         ?>
<a href="index.php?action=motd">MOTD</a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("pkg_settings")){
         ?>
<a href="?action=pkg_settings">Paketquelle</a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("logo")){
         ?>
<a href="index.php?action=logo_upload">Logo</a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("languages")){
         ?>
<a href="index.php?action=languages">Sprachen</a>
<br/>
<?php }
     ?>
<?php if($acl -> hasPermission("other")){
         ?>
<a href="?action=other_settings">Sonstiges</a>
<?php }
     ?>
</p>
<?php
     }else{
     noperms();
     }

?>
