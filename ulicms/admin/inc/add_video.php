<?php
$acl = new ACL();
if($acl -> hasPermission("videos")){
?>
<h1><?php translate("UPLOAD_VIDEO");?></h1>
<form action="#" method="post" enctype="multipart/form-data">
<strong><?php translate("name");?></strong><br/>
<input type="text" name="name" required="true" value="" maxlength=255/>
<br/><br/>
<strong><?php echo translate("video_ogg");?></strong><br/>
<input name="video_ogg" type="file"><br/><br/>
<strong><?php echo translate("video_mp4");?></strong><br/>
<input name="video_mp4" type="file"><br/><br/>
<strong><?php translate("width");?></strong><br/>
<input type="number" name="width" value="1280" step="1">

<br/><br/>
<strong><?php translate("height");?></strong><br/>
<input type="number" name="height" value="720" step="1">

<br/><br/>
<input type="submit" value="<?php translate("UPLOAD_VIDEO");?>">
</form>
<?php 
} 
else {
   noperms();
}