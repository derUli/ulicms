<?php if(defined("_SECURITY")){
     $acl = new ACL();
     if($acl -> hasPermission("banners")){
         ?>

<form action="index.php?action=banner" method="post"> 
<p><input type="radio" checked="checked" id="radio_gif" name="type" value="gif" onclick="$('#type_gif').slideDown();$('#type_html').slideUp();"><label for="radio_gif">GIF-Banner</label></p>
<fieldset id="type_gif">
<input type="hidden" name="add_banner" value="add_banner">
<strong data-tooltip="Dieser Text erscheint, wenn man mit der Maus über den Banner fährt...">Bannertext:</strong><br/>
<input type="text" style="width:300px;" name="banner_name" value="">
<br/><br/>
<strong data-tooltip="Die Adresse der Grafikdatei">Bild-URL:</strong><br/>
<input type="text" style="width:300px;" name="image_url" value="">
<br/><br/>
<strong data-tooltip="Wohin soll der Banner verlinken?">Link-URL:</strong><br/>
<input type="text" style="width:300px;" name="link_url" value="">
<br/>
</fieldset>

<p><input type="radio" id="radio_html" name="type" value="html" onclick="$('#type_html').slideDown();$('#type_gif').slideUp();"><label for="radio_html">HTML</label></p>

<fieldset id="type_html" style="display:none">
<textarea name="html" rows=10 cols=40></textarea>
</fieldset>
<br/>

<strong>Kategorie:</strong><br/>
<?php echo categories :: getHTMLSelect()?>

<br/>
<br/>
<input type="submit" value="Datensatz hinzufügen">
<?php
         if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
             ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
         ?>
</form>
<?php
        
         }
    else{
         noperms();
         }
    
     ?>




<?php }
?>
