<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=30){

?>
<form name="newpageform" action="index.php?action=pages" method="post">
<input type="hidden" name="add_page" value="add_page">
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

<strong data-tooltip="In welchem Menü soll diese Seite angezeigt werden?">Menü:</strong><br/>
<select name="menu" size=1>
<option value="top">Oben</option>
<option value="down">Unten</option>
<option value="left">Links</option>
<option value="right">Rechts</option>
<option value="none">Nicht im Menü</option>
</select><br/> <br/>

<strong data-tooltip="Die Position dieser Seite im Menü">Position:</strong><br/>
<input type="text" name="position" value="<?php echo $row->position;?>">
              
<br/><br/>

<strong data-tooltip="Wenn das eine Unterseite werden sollte.">Übergeordnete Seite:</strong><br/>
<select name="parent" size=1>
<option selected="selected" value="-">-</option>
<?php foreach(getAllSystemNames() as $systemname){?>
	<option value="<?php echo $systemname;?>">
	<?php echo $systemname;?>
	</option>
<?php
	}
?>
</select><br/> <br/>

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
<select name="access" size=4 multiple>
<option value="all" selected>Alle</option>
<option value="registered">Registrierte Benutzer</option>
<option value="admin">Admin</option>
</select>


<br/><br/>
<strong data-tooltip="Sollen Kommentare aktiviert sein?">Kommentare:</strong><br/>
<select name="comments_enabled" size=1>
<option value="1">aktiviert</option>
<option value="0">deaktiviert</option>
</select>

<br/><br/>

<strong data-tooltip="Wählen Sie diese Option aus bei zeitkritischen Inhalten, wie Aktuelles, Pressemitteilungen und bei Inhalten die sich häufig ändern.">In RSS-Feed aufnehmen:</strong><br/>
<select name="notinfeed" size=1>
<option value="0">Ja</option>
<option value="1">Nein</option>
</select>


<br/><br/>

<textarea name="page_content" id="page_content" style="display:none;"></textarea>
<script type="text/javascript">
document.getElementById("page_content").style.display="block";
var editor = CKEDITOR.replace( 'page_content',
					{
						skin : 'kama'
					});
					
</script>
<noscript>
<p style="color:red;">Der Editor benötigt JavaScript. Bitte aktivieren Sie JavaScript. <a href="http://jumk.de/javascript.html" target="_blank">[Anleitung]</a></p>
</noscript>
<br/><br/>
</form>

<?php
}
else{
noperms();
}
?>

<?php }?>
