<?php
$acl = new ACL();

if($acl -> hasPermission("videos")){
?>
<h1>Videos</h1>
Comming Soon!


<?php 
} 
else {
   noperms();
}