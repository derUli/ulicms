<?php
if(!defined("ULICMS_ROOT"))
   die("Schlechter Hacker!");

$acl = new ACL();

if(!$acl->hasPermission("export")){
   noperms();
} else {
?>
  <h1>JSON Export</h1>
  
  <?php
  }

  ?>
