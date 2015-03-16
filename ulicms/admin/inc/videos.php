<?php
$acl = new ACL();
$all_videos = db_query("SELECT * FROM ".tbname("videos")." ORDER by id");

if($acl -> hasPermission("videos")){
?>
<h1><?php translate("videos");?></h1>
Coming Soon!


<?php 
} 
else {
   noperms();
}