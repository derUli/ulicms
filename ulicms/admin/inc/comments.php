<?php
if(!defined("ULICMS_ROOT"))
     die("schlechter Hacker");

$acl = new ACL();

if($acl -> hasPermission("comments")){
?>

<?php 
} else {
     noperms();
}
?>