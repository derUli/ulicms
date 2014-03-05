<?php
if(!defined("ULICMS_ROOT"))
     die("Bad Hacker!");

$acl = new ACL();

if($acl -> hasPermission("images") or $acl -> hasPermission("flash") or $acl -> hasPermission("files")){
    
    
     ?>

<h2>Medien</h2>
<strong>Bitte wählen Sie einen Dateityp aus:</strong><br/>
<?php if($acl -> hasPermission("images")){
         ?>
<a href="index.php?action=images">Bilder</a><br/>
<?php }
     ?>
<?php if($acl -> hasPermission("flash")){
         ?>
<a href="index.php?action=flash">Flash</a><br/>
<?php }
     ?>
<?php if($acl -> hasPermission("files")){
         ?>
<a href="index.php?action=files">Dateien</a><br/>
<?php }
     ?>






<?php }else{
     noperms();
    
     }
?>
