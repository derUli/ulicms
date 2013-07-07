<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=30){
	$page=mysql_real_escape_string($_GET["page"]);
	$query=db_query("SELECT * FROM ".tbname("content")." WHERE id='$page'");
	while($row=mysql_fetch_object($query)){

?>

<form id="pageform" action="index.php?action=pages" method="post">
<input type="hidden" name="edit_page" value="edit_page">


<input type="hidden" name="page_id" value="<?php echo $row->id?>">

<strong data-tooltip="Dieser Name wird für die Adresse benötigt.
Beim Eingeben des Seitentitels wird er automatisch generiert">Permalink:</strong><br/>
<input type="text" style="width:300px;" name="page_" value="<?php echo $row->systemname?>">
<br/><br/>
<strong data-tooltip="Hier können Sie den Titel der Seite ändern">Seitentitel:</strong><br/>
<input type="text" style="width:300px;" name="page_title" value='<?php 
echo htmlspecialchars($row->title);

?>'>
<br/><br/>
<strong data-tooltip="Soll diese Seite keinen eigenen Inhalt enthalten sondern stattdessen auf eine Externe Seite verlinken, tragen Sie die hier die URL ein.
Wenn Sie z.B. http://www.google.de eintragen, verweist der Menüpunkt zur Google Startseite">Externer Link:</strong><br/>
<input type="text" style="width:300px;" name="redirection" value="<?php echo $row->redirection;?>">

<br/><br/>
<strong data-tooltip="In welchem Menü soll diese Seite angezeigt werden?">Menü</strong><br/>
<select name="menu" size=1>
<option <?php if($row->menu =="top"){echo 'selected="selected" ';}?>value="top">Oben</option>
<option <?php if($row->menu =="bottom"){echo 'selected="selected" ';}?> value="bottom">Unten</option>
<option <?php if($row->menu =="left"){echo 'selected="selected" ';}?> value="left">Links</option>
<option <?php if($row->menu =="right"){echo 'selected="selected" ';}?> value="right">Rechts</option>
<option <?php if($row->menu =="none"){echo 'selected="selected" ';}?> value="none">Nicht im Menü</option>
</select><br/><br/>

<strong data-tooltip="Die Position dieser Seite im Menü">Position:</strong><br/>
<input type="text" name="position" value="<?php echo $row->position;?>">
              
<br/><br/>
<strong data-tooltip="Wenn das eine Unterseite werden sollte.">Übergeordnete Seite:</strong><br/>
<select name="parent" size=1>
<option value="NULL">-</option>
<?php foreach(getAllSystemNames() as $systemname){?>
	<option value="<?php echo getPageIDBySystemname($systemname);?>"<?php if(getPageIDBySystemname($systemname) == $row->parent){
	echo " selected='selected'";}?>><?php echo $systemname;?></option>
<?php
	}
?>
</select>
<br/><br/>

<strong data-tooltip="In welchem Fenster soll diese Seite geöffnet werden?">Öffnen in</strong><br/>
<select name="target" size=1>
<option <?php if($row->target == "_self"){ echo 'selected="selected" ';}?>value="_self">In diesem Fenster</option>
<option <?php if($row->target == "_blank"){ echo 'selected="selected" ';} ?>value="_blank">In neuem Fenster</option>
</select>

<br/><br/>

<strong data-tooltip="Soll die Seite für die Öffentlichkeit sichtbar sein?">aktiviert:</strong><br/>
<select name="activated" size=1>
<option value="1" <?php if($row->active==1){echo "selected";}?>>aktiviert</option>
<option value="0" <?php if($row->active==0){echo "selected";}?>>deaktiviert</option>
</select>
<br/>

<br/>

<strong data-tooltip="Für welche Benutzergruppen soll diese Seite sichtbar sein?">Sichtbar für:</strong><br/>
<?php $access = explode(",", $row->access);?>
<select name="access[]" size=4 multiple>
<option value="all"<?php if(in_array("all", $access)) echo " selected"?>>Alle</option>
<option value="registered"<?php if(in_array("registered", $access)) echo " selected"?>>Registrierte Benutzer</option>
<option value="admin"<?php if(in_array("admin", $access)) echo " selected"?>>Admin</option>
</select>


<br/><br/>

<strong data-tooltip="Eine kurze Beschreibung dieser Seite für Suchmaschinen">Meta Description:</strong><br/>
<input type="text" style="width:300px;" name="meta_description" value='<?php 
echo htmlspecialchars($row->meta_description); ?>'>

<br/><br/>

<strong data-tooltip="Stichworte dieser Seite für Suchmaschinen
Mit Komma getrennt">Meta Keywords:</strong><br/>
<input type="text" style="width:300px;" name="meta_keywords" value='<?php 
echo htmlspecialchars($row->meta_keywords); ?>'>
 
<br/><br/>
<strong data-tooltip="Sollen Kommentare aktiviert sein?">Kommentare:</strong><br/>
<select name="comments_enabled" size=1>
<option value="1" <?php if($row->comments_enabled == 1){echo "selected";}?>>aktiviert</option>
<option value="0" <?php if($row->comments_enabled == 0){echo "selected";}?>>deaktiviert</option>
</select>
<br/><br/>
<div align="center">
<textarea name="page_content" id="page_content" cols=60 rows=20><?php echo htmlspecialchars($row->content);?></textarea>
<script type="text/javascript">
var editor = CKEDITOR.replace( 'page_content',
					{
						skin : 'kama'
					});                                         

editor.on("instanceReady", function()
{
	this.document.on("keyup", CKCHANGED);
	this.document.on("paste", CKCHANGED);
}
);
function CKCHANGED() { 
	formchanged = 1;
}					
			
var formchanged = 0;
var submitted = 0;
 
$(document).ready(function() {
	$('form').each(function(i,n){
		$('input', n).change(function(){formchanged = 1});
		$('textarea', n).change(function(){formchanged = 1});
		$('select', n).change(function(){formchanged = 1}); 
		$(n).submit(function(){submitted=1});
	});
});
 
window.onbeforeunload = confirmExit;
function confirmExit()
{
	if(formchanged == 1 && submitted == 0)
		return "Wenn Sie diese Seite verlassen gehen nicht gespeicherte Änderungen verloren.";
	else 
		return;
}			
</script>
<noscript>
<p style="color:red;">Der Editor benötigt JavaScript. Bitte aktivieren Sie JavaScript. <a href="http://jumk.de/javascript.html" target="_blank">[Anleitung]</a></p>

</noscript>
<div class="inPageMessage">
<div id="message_page_edit" class="inPageMessage"></div>
<img class="loading" src="gfx/loading.gif" alt="Wird gespeichert...">
</div>

<input type="submit" value="Speichern">
</div>
</form>

<script type="text/javascript">
$("#pageform").ajaxForm({beforeSubmit: function(e){
  $("#message_page_edit").html("");
  $("#message_page_edit").hide();
  $(".loading").show();
  }, 
  success:function(e){
  $(".loading").hide();  
  $("#message_page_edit").html("<span style=\"color:green;\">Die Seite wurde gespeichert</span>");
  $("#message_page_edit").show();
  }
  
}); 

</script>



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

<?php }?>
