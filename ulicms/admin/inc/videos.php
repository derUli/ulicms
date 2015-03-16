<?php
$acl = new ACL();
$all_videos = db_query("SELECT * FROM ".tbname("videos")." ORDER by id");

if($acl -> hasPermission("videos")){
?>
<h1><?php translate("videos");?></h1>
<p><a href="index.php?action=add_video">[<?php translate("upload_video");?>]</a></p>
<p>Coming Soon!</p>


<?php 
} 
else {
   noperms();
}