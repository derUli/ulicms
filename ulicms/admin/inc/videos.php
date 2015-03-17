<?php
$acl = new ACL();

$video_folder = ULICMS_ROOT ."/content/videos";
if(!is_dir($video_folder))
   mkdir($video_folder);

   
if($acl -> hasPermission("videos") and isset($_FILES)){
   $mp4_file_value = "";
   // MP4
   if(!empty($_FILES['mp4_file']['name'])){
   $mp4_file = time()."-".basename($_FILES['mp4_file']['name']);
   $mp4_type = $_FILES['mp4_file']["type"];
   $mp4_allowed_mime_type = array("video/mp4");
   if(in_array($mp4_type, $mp4_allowed_mime_type)){
      $target = $video_folder."/".$mp4_file;
      if(move_uploaded_file($_FILES['mp4_file']['tmp_name'], $target)){ 
         $mp4_file_value = "content/videos/".$mp4_file;
      }
   }
   }
   
      $ogg_file_value = "";
   // ogg
   if(!empty($_FILES['ogg_file']['name'])){
   $ogg_file = time()."-".$_FILES['ogg_file']['name'];
   $ogg_type = $_FILES['ogg_file']["type"];
   $ogg_allowed_mime_type = array("video/ogg", "application/ogg");
   if(in_array($ogg_type, $ogg_allowed_mime_type)){
      $target = $video_folder."/".$ogg_file;
      if(move_uploaded_file($_FILES['ogg_file']['tmp_name'], $target)){ 
         $ogg_file_value = "content/videos/".$ogg_file;
      }
   }
   
   }
   
   $name = db_escape($_POST["name"]);
   $ogg_file_value = db_escape($ogg_file_value);
   $mp4_file_value = db_escape($mp4_file_value);
   
   $width = intval($_POST["width"]);
   $height = intval($_POST["height"]);
   $timestamp = time();
   
   if(!empty($ogg_file_value) or !empty($mp4_file_value)){
      db_query("INSERT INTO ".tbname("videos")." (name, ogg_file, mp4_file, width, height, created, `updated`) VALUES ('$name', '$ogg_file_value', '$mp4_file_value', $width, $height, $timestamp, $timestamp);")or die(db_error());
   }
   
}


$all_videos = db_query("SELECT id, name, mp4_file, ogg_file FROM ".tbname("videos")." ORDER by id");

if($acl -> hasPermission("videos")){
?>
<h1><?php translate("videos");?></h1>
<p><a href="index.php?action=add_video">[<?php translate("upload_video");?>]</a></p>

<table class="tablesorter">
<thead>
<tr>
<th><?php translate("id");?></th>
<th><?php translate("name");?></th>
<th><?php translate("ogg_file");?></th>
<th><?php translate("mp4_file");?></th>
<td></td>
<td></td>
</tr>
<tbody>
<?php 
while($row = db_fetch_object($all_videos)){
?>
<tr>
<td><?php echo $row->id;?></td>
<td><?php echo htmlspecialchars($row->name);?></td>
<td><?php echo htmlspecialchars(basename($row->ogg_file));?></td>
<td><?php echo htmlspecialchars(basename($row->mp4_file));?></td>
<td><a href="#" onclick="alert('Coming soon!'); return false;"><img src="gfx/edit.png"  class="mobile-big-image" alt="<?php translate("edit");?>" title="<?php translate("edit");?>"></a></td>
<td><a href="#" onclick="alert('Coming soon!'); return false;"><img src="gfx/delete.png" class="mobile-big-image" alt="<?php translate("delete");?>" title="<?php translate("delete");?>"></a></td>
</tr>
<?php }?>
</tbody>
</thead>

<?php 
} 
else {
   noperms();
}