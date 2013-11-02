<?php if(defined("_SECURITY")){
     if($_SESSION["group"] >= 40){
         $banner = db_real_escape_string($_GET["banner"]);
         $query = db_query("SELECT * FROM " . tbname("banner") . " WHERE id='$banner'");
         while($row = db_fetch_object($query)){
             ?>

<form action="index.php?action=banner" method="post">
<h4>Vorschau</h4>
<p><a href="<?php echo $row -> link_url;
             ?>" target="_blank"><img src="<?php echo $row -> image_url;
             ?>" title="<?php echo $row -> name;
             ?>" alt="<?php echo $row -> name;
             ?>" border=0></a></p>
<input type="hidden" name="edit_banner" value="edit_banner">
<input type="hidden" name="id" value="<?php echo $row -> id;
             ?>">
<strong data-tooltip="Dieser Text erscheint, wenn man mit der Maus über den Banner fährt...">Bannertext:</strong><br/>
<input type="text" style="width:300px;" name="banner_name" value="<?php echo $row -> name;
             ?>">
<br/><br/>
<strong data-tooltip="Die Adresse der Grafikdatei...">Bild-URL:</strong><br/>
<input type="text" style="width:300px;" name="image_url" value="<?php echo $row -> image_url;
             ?>">
<br/><br/>
<strong data-tooltip="Wohin soll der Banner verlinken?">Link-URL:</strong><br/>
<input type="text" style="width:300px;" name="link_url" value="<?php echo $row -> link_url;
             ?>">

<br/>

<br/><br/>
<input type="submit" value="OK">
<?php 
if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }?>
</form>

<?php
             break;
             }
         ?>
<?php
        
         }
    else{
         noperms();
         }
    
     ?>




<?php }
?>
