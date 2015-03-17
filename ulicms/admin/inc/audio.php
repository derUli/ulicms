<?php
$acl = new ACL();

$audio_folder = ULICMS_ROOT ."/content/audio";
if(!is_dir($audio_folder))
   mkdir($audio_folder);
if($acl -> hasPermission("audio") and isset($_REQUEST["delete"])){
   $query = db_query("select ogg_file, mp3_file from ".tbname("audio"). " where id = ".intval($_REQUEST["delete"]));
   if(db_num_rows($query) > 0 ){
       $result = db_fetch_object($query); 
       $filepath = ULICMS_ROOT."/content/audio/".basename($result->ogg_file);
       if(!empty($result->ogg_file) and is_file($filepath)){
          @unlink($filepath);
       }
                
                  $filepath = ULICMS_ROOT."/content/audio/".basename($result->mp3_file);
        if(!empty($result->mp3_file) and is_file($filepath)){

          @unlink($filepath);
       }
       
       db_query("DELETE FROM ".tbname("audio"). " where id = ".$_REQUEST["delete"]);
   }
}
else if($acl -> hasPermission("audio") and isset($_REQUEST["update"])){
   $name = db_escape($_POST["name"]);
   $id = intval($_POST["id"]);
   $ogg_file = db_escape(basename($_POST["ogg_file"]));
   $mp3_file = db_escape(basename($_POST["mp3_file"]));
   $width = intval($_POST["width"]);
   $height = intval($_POST["height"]);
   $updated = time();
   $category_id = intval($_POST["category"]);
   
   db_query("UPDATE ".tbname("audio"). " SET name='$name', ogg_file='$ogg_file', mp3_file='$mp3_file', width=$width, height=$height, category_id = $category_id, `updated` = $updated where id = $id")or die(db_error());
}
   
else if($acl -> hasPermission("audio") and isset($_FILES) and isset($_REQUEST["add"])){
   $mp3_file_value = "";
   // mp3
   if(!empty($_FILES['mp3_file']['name'])){
   $mp3_file = time()."-".basename($_FILES['mp3_file']['name']);
   $mp3_type = $_FILES['mp3_file']["type"];
   $mp3_allowed_mime_type = array("audio/mp3");
   if(in_array($mp3_type, $mp3_allowed_mime_type)){
      $target = $audio_folder."/".$mp3_file;
      if(move_uploaded_file($_FILES['mp3_file']['tmp_name'], $target)){ 
         $mp3_file_value = basename($mp3_file);
      }
   }
   }
   
      $ogg_file_value = "";
   // ogg
   if(!empty($_FILES['ogg_file']['name'])){
   $ogg_file = time()."-".$_FILES['ogg_file']['name'];
   $ogg_type = $_FILES['ogg_file']["type"];
   $ogg_allowed_mime_type = array("audio/ogg", "application/ogg", "audio/ogg");
   if(in_array($ogg_type, $ogg_allowed_mime_type)){
      $target = $audio_folder."/".$ogg_file;
      if(move_uploaded_file($_FILES['ogg_file']['tmp_name'], $target)){ 
         $ogg_file_value = basename($ogg_file);
      }
   }
   
   }
   
   $name = db_escape($_POST["name"]);
   $category_id = intval($_POST["category"]);
   $ogg_file_value = db_escape($ogg_file_value);
   $mp3_file_value = db_escape($mp3_file_value);
   
   $width = intval($_POST["width"]);
   $height = intval($_POST["height"]);
   $timestamp = time();
   
   if(!empty($ogg_file_value) or !empty($mp3_file_value)){
      db_query("INSERT INTO ".tbname("audio")." (name, ogg_file, mp3_file, width, height, created, category_id, `updated`) VALUES ('$name', '$ogg_file_value', '$mp3_file_value', $width, $height, $timestamp, $category_id, $timestamp);")or die(db_error());
   }
   
}


if(!isset($_SESSION["filter_category"])){
  $_SESSION["filter_category"] = 0;
  }
  
 if(isset($_GET["filter_category"])){
             $_SESSION["filter_category"] = intval($_GET["filter_category"]);
            
             }

$sql = "SELECT id, name, mp3_file, ogg_file FROM ".tbname("audio")." ";
if($_SESSION["filter_category"] > 0){
  $sql .= " where category_id = ".$_SESSION["filter_category"]." ";
}
$sql .= " ORDER by id";

$all_audio = db_query($sql);


if($acl -> hasPermission("audio")){
?>
<script type="text/javascript">
$(window).load(function(){
   $('#category').on('change', function (e) {
   var valueSelected = $('#category').val();
     location.replace("index.php?action=audio&filter_category=" + valueSelected)
   
   });

});
</script>
<h1><?php translate("audio");?></h1>
<?php echo TRANSLATION_CATEGORY;
         ?> 
<?php
         echo categories :: getHTMLSelect($_SESSION["filter_category"], true);
         ?>
<br/>
<br/>
<p><a href="index.php?action=add_audio">[<?php translate("upload_audio");?>]</a></p>
<table class="tablesorter">
<thead>
<tr>
<th><?php translate("id");?></th>
<th><?php translate("name");?></th>
<th><?php translate("OGG_FILE");?></th>
<th><?php translate("MP3_FILE");?></th>
<td></td>
<td></td>
</tr>
<tbody>
<?php 
while($row = db_fetch_object($all_audio)){
?>
<tr>
<td><?php echo $row->id;?></td>
<td><?php echo htmlspecialchars($row->name);?></td>
<td><?php echo htmlspecialchars(basename($row->ogg_file));?></td>
<td><?php echo htmlspecialchars(basename($row->mp3_file));?></td>
<td><a href="index.php?action=edit_audio&id=<?php echo $row->id;?>"><img src="gfx/edit.png"  class="mobile-big-image" alt="<?php translate("edit");?>" title="<?php translate("edit");?>"></a></td>
<td><a href="index.php?action=audio&delete=<?php echo $row->id;?>" onclick="return confirm('<?php translate("ASK_FOR_DELETE");?>')"><img src="gfx/delete.png" class="mobile-big-image" alt="<?php translate("delete");?>" title="<?php translate("delete");?>"></a></td>
</tr>
<?php }?>
</tbody>
</thead>
</table>

<?php 
} 
else {
   noperms();
}