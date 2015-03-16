<?php
$acl = new ACL();
if($acl -> hasPermission("videos")){
?>
<h1><?php translate("UPLOAD_VIDEO");?></h1>
<?php 
} 
else {
   noperms();
}