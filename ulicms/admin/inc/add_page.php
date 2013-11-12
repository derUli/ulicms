<?php if(defined("_SECURITY")){
    $acl = new ACL();
     if($acl -> hasPermission("pages")){
        
         ?>
<form id="pageform" name="newpageform" action="index.php?action=pages" method="post">

<strong data-tooltip="Dieser Name wird für die Adresse benötigt.
Beim Eingeben des Seitentitels wird er automatisch generiert">Permalink:</strong><br/>
<input type="text" style="width:300px;" name="system_title" value="">
<br/><br/>

<strong data-tooltip="Der Titel der Seite">Seitentitel:</strong><br/>
<input type="text" style="width:300px;" name="page_title" value="" onkeyup="systemname_vorschlagen(this.value)">
<br/><br/>
<strong data-tooltip="Soll diese Seite keinen eigenen Inhalt enthalten sondern stattdessen auf eine Externe Seite verlinken, tragen Sie die hier die URL ein.
Wenn Sie z.B. http://www.google.de eintragen, verweist der Menüpunkt zur Google Startseite">Externer Link:</strong><br/>
<input type="text" style="width:300px;" name="redirection" value="">
<br/><br/>
<strong data-tooltip="In welcher Sprache ist diese Seite verfasst?">Sprache:</strong>
<br/>
<select name="language">
<?php
         $languages = getAllLanguages();
         if(!empty($_SESSION["filter_language"])){
             $default_language = $_SESSION["filter_language"];
            
             }
        else{
             $default_language = getconfig("default_language");
             }
        
        
         for($j = 0; $j < count($languages); $j++){
             if($languages[$j] === $default_language){
                 echo "<option value='" . $languages[$j] . "' selected>" . $languages[$j] . "</option>";
                 }else{
                 echo "<option value='" . $languages[$j] . "'>" . $languages[$j] . "</option>";
                 }
            
            
             }
         ?>
</select>
<br/><br/>

<strong>Kategorie</strong><br/>
<?php echo categories::getHTMLSelect()?>
<br/><br/>

<strong data-tooltip="In welchem Menü soll diese Seite angezeigt werden?">Menü:</strong><br/>
<select name="menu" size=1>
<?php
         foreach(getAllMenus() as $menu){
             ?>
<option value="<?php echo $menu?>"><?php echo $menu;
             ?></option>
<?php
            
             }
         ?>
</select><br/> <br/>

<strong data-tooltip="Die Position dieser Seite im Menü">Position:</strong><br/>
<input type="text" name="position" value="0">
              
<br/><br/>

<strong data-tooltip="Wenn das eine Unterseite werden sollte.">Übergeordnete Seite:</strong><br/>
<select name="parent" size=1>
<option selected="selected" value="NULL">-</option>
<?php foreach(getAllSystemNames() as $systemname){
             ?>
	<option value="<?php echo getPageIDBySystemname($systemname);
             ?>">
	<?php echo $systemname;
             ?>
	</option>
<?php
             }
         ?>
</select>

<br/><br/>

<strong data-tooltip="In welchem Fenster soll diese Seite geöffnet werden?">Öffnen in</strong><br/>
<select name="target" size=1>
<option value="_self">In diesem Fenster</option>
<option value="_blank">In neuem Fenster</option>
</select>


<br/> <br/>

<script type="text/javascript">
function systemname_vorschlagen(txt){
var systemname=txt.toLowerCase();
systemname=systemname.replace(/ü/g,"ue");
systemname=systemname.replace(/ö/g,"oe");
systemname=systemname.replace(/ä/g,"ae");
systemname=systemname.replace(/Ã/g,"ss");
systemname=systemname.replace(/\040/g,"_");
systemname=systemname.replace(/\?/g,"");
systemname=systemname.replace(/\!/g,"");
systemname=systemname.replace(/\"/g,"");
systemname=systemname.replace(/\'/g,"");
systemname=systemname.replace(/\+/g,"");
systemname=systemname.replace(/\&/g,"");
systemname=systemname.replace(/\#/g,"");
document.newpageform.system_title.value=systemname
}
</script>

<strong data-tooltip="Soll die Seite öffentlich zugänglich sein?">aktiviert:</strong><br/>
<select name="activated" size=1>
<option value="1">aktiviert</option>
<option value="0">deaktiviert</option>
</select>
<br/>
<br/>


<strong data-tooltip="Für welche Benutzergruppen soll diese Seite sichtbar sein?">Sichtbar für:</strong><br/>
<select name="access[]" size=4 multiple>
<option value="all" selected>Alle</option>
<option value="registered">Registrierte Benutzer</option>
<option value="admin">Admin</option>
</select>


<br/><br/>

<strong data-tooltip="Eine kurze Beschreibung dieser Seite für Suchmaschinen">Meta Description:</strong><br/>
<input type="text" style="width:300px;" name="meta_description" value=''>
<br/><br/>

<strong data-tooltip="Stichworte dieser Seite für Suchmaschinen
Mit Komma getrennt">Meta Keywords:</strong><br/>
<input type="text" style="width:300px;" name="meta_keywords" value=''>
 
<br/><br/>

<strong data-tooltip="Sollen Kommentare aktiviert sein?">Kommentare:</strong><br/>
<select name="comments_enabled" size=1>
<option value="1">aktiviert</option>
<option value="0" selected="selected">deaktiviert</option>
</select>
<?php add_hook("page_option");
         ?>
<br/><br/>

<div align="center">
<textarea name="page_content" id="page_content" cols=60 rows=20></textarea>
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
</div>
<input type="hidden" name="add_page" value="add_page">

<input type="submit" value="Speichern">
</div>
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
