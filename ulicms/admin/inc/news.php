<?php
if(!defined("ULICMS_ROOT"))
     die("schlechter Hacker");

$acl = new ACL();

if($acl -> hasPermission("news")){
?>

<?php 
} else {
     noperms();
}
?>