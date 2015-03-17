<?php
$acl = new ACL();
if($acl -> hasPermission("videos")){

$id = intval($_REQUEST["id"]);
$query = db_query("SELECT * FROM ".tbname("videos"). " WHERE id = $id");
if(db_num_rows($query) > 0){
$result = db_fetch_object($query);
?>
<h1><?php translate("UPLOAD_VIDEO");?></h1>
<form action="index.php?action=videos" method="post">
<?php csrf_token_html();?>
<input type="hidden" name="id" value="<?php echo $result->id;?>">
<input type="hidden" name="update" value="update">
<strong><?php translate("name");?></strong><br/>
<input type="text" name="name" required="true" value="<?php echo htmlspecialchars($result->name);?>" maxlength=255/>
<br/><br/>
<strong><?php echo translate("video_ogg");?></strong><br/>
<input name="ogg_file" type="text" value="<?php echo htmlspecialchars($result->ogg_file);?>"><br/><br/>
<strong><?php echo translate("ogg_file");?></strong><br/>
<input name="mp4_file" type="text" value="<?php echo htmlspecialchars($result->mp4_file);?>"><br/><br/>
<strong><?php translate("width");?></strong><br/>
<input type="number" name="width" value="<?php echo $result->width;?>" step="1">

<br/><br/>
<strong><?php translate("height");?></strong><br/>
<input type="number" name="height" value="<?php echo $result->height;?>" step="1">
<br/><br/>
<input type="submit" value="<?php translate("UPLOAD_VIDEO");?>">
</form>
<?php 

} else {
   echo "video not found!";
}


} 
else {
   noperms();
}