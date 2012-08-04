<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=50){

	$query=mysql_query("SELECT * FROM ".tbname("settings")." ORDER BY name",$connection);
	$settings=Array();
	while($row=mysql_fetch_object($query)){
		
		$settings[$row->name]=$row->value;

	}

	$query2 = mysql_query("SELECT * FROM ".tbname("content"). " ORDER BY systemname");
	$pages = Array();

	while($row = mysql_fetch_object($query2)){
		array_push($pages, $row->systemname);
	}
?>

<h2>Einstellungen</h2>
<p>Hier können Sie die Einstellungen für Ihre Internetseite verändern.</p>
<form action="index.php?action=save_settings" method="post">
<table border=1>
<tr>
<td><strong data-tooltip="Der Name dieser Webpräsenz.">Titel der Homepage:</strong></td>
<td><strong><input type="text" name="homepage_title" size=35 value="<?php echo $settings["homepage_title"];?>"></strong></td>
</tr>
<tr>
<td><strong data-tooltip="Eine kurze Beschreibung oder ein Slogan um was es auf dieser Webpräsenz geht.">Motto der Homepage:</strong></td>
<td><strong><input type="text" name="homepage_motto" size=35 value="<?php echo $settings["motto"];?>"></strong></td>
</tr>
<tr>
<td><strong data-tooltip="Der Name des Inhabers dieser Webpräsenz...">Inhaber der Homepage:</strong></td>
<td><strong><input type="text" name="homepage_owner" size=35 value="<?php echo $settings["homepage_owner"];?>"></strong></td>
</tr>
<tr>
<td><strong data-tooltip="An diese Adresse werden Emails über das Kontaktformular versandt...">Emailadresse des Inhabers:</strong></td>
<td><strong><input type="text" name="email" size=35 value="<?php echo $settings["email"];?>"></strong></td>
</tr>
<tr>
<td><strong data-tooltip="Wie viele News sollen angezeigt werden?">Die letzten X News anzeigen:</strong></td>
<td><strong><input type="text" name="max_news" size=35 value="<?php echo $settings["max_news"];?>"></strong></td>
</tr>
<tr>
<td><strong data-tooltip="Wie viele Einträge sollen im RSS-Feed angezeigt werden?">Anzahl der Einträge im RSS-Feed</strong></td>
<td><input type="text" name="items_in_rss_feed" size=35 value="<?php echo $settings["items_in_rss_feed"];?>"></td>
</tr>
<tr>
<td>
<strong data-tooltip="Dies ist die Startseite Ihres Internetauftritts...">Startseite</strong>
</td>
<td>

<select name="frontpage" size=1 style="width:100%;">
<?php for($i=0;$i<count($pages);$i++){
if($pages[$i] == $settings["frontpage"]){
echo "<option value='".$pages[$i]."' selected='selected'>".$pages[$i]."</option>";
}else{
echo "<option value='".$pages[$i]."'>".$pages[$i]."</option>";
}

}
?>
</select>


</td>
</tr>
<tr>
<td><strong><a href="http://www.i18nguy.com/unicode/language-identifiers.html" target="_blank">Sprachcode</a></td>
<td><strong><input type="text" name="language" size=35 value="<?php echo $settings["language"];?>"></strong></td>
</tr>
<tr>
<td><strong data-tooltip="Wenn Sie grundlegende Änderungen an Ihrer Webpräsenz vornehmen möchten, können Sie Ihre Seite solange für Besucher sperren und stattdessen eine Wartungsmeldung anzeigen. Diese können Sie in der Template maintenance.php anpassen.">Wartungsmodus aktiviert:</strong></td>
<td><strong><input type="checkbox" name="maintenance_mode" <?php
if(strtolower($settings["maintenance_mode"]=="on")||$settings["maintenance_mode"]=="1"||strtolower($settings["maintenance_mode"])=="true"){
echo " checked";


}

?>
></strong></td>
</tr>
<tr>
<td><strong data-tooltip="Dann können sich Gäste ein Benutzerkonto anlegen.">Gäste dürfen sich registrieren:</strong></td>
<td><strong><input type="checkbox" name="visitors_can_register" <?php
if(strtolower($settings["visitors_can_register"]=="on")||
$settings["visitors_can_register"]=="1"||
strtolower($settings["visitors_can_register"])=="true"){
echo " checked";


}

?>
></strong></td>
</tr>
<tr>
<td></td>
<td><strong data-tooltip="Zusätzliche Informationen die für Optimierung des Suchmaschinen-Rankings dienen...">Meta-Daten für Suchmaschinen:</strong></td>
</tr>
<tr>
<td><strong data-tooltip="Stichwörter, die den Inhalt dieser Website beschreiben...">Keywords:</strong></td>
<td><input type="text" name="meta_keywords" value="<?php echo $settings["meta_keywords"];?>" size="35">
</tr>
<tr>
<td><strong data-tooltip="Eine kurze Beschreibung der Website....">Beschreibung:</strong></td>
<td><input type="text" name="meta_description" value="<?php echo $settings["meta_description"];?>" size="35">
</tr>
<tr>
<td></td>
<td><strong>Kommentare:</strong>
</td>
</tr>
<tr>
<td><strong data-tooltip="Welches Kommentarsystem soll verwendet werden?
UliCMS verf&uuml;gt &uuml;ber direkte Schnittstellen zu Facebook und Disqus">Kommentarsystem</td>
<td>
<select name="comment_mode" size=1 style="width:100%;">
<option value="facebook" <?php if($settings["comment_mode"] == "facebook"){echo 'selected';}?>>Facebook Comments</option>
<option value="disqus" <?php if($settings["comment_mode"] == "disqus"){echo 'selected';}?>>Disqus</option>
<option value="off" <?php if($settings["comment_mode"] == "off"){echo 'selected';}?>>Aus</option>
</select>
</td>
</tr>
<tr>
<td><strong data-tooltip="Die Facebook-ID wird ben&ouml;tigt, damit Sie wenn Sie wenn Sie die Kommentarfunktion von
Facebook nutzen, die Kommentare moderieren k&ouml;nnen">Facebook-ID:</strong></td>
<td><input type="text" name="facebook_id" value="<?php echo $settings["facebook_id"];?>" size="35">
</tr>
<tr>
<td><strong data-tooltip="Der Disqus-Shortname wird ben&ouml;tigt, damit Sie Sie 
die Kommentarfunktion von disqus verwenden k&ouml;nnen.
Daf&uuml;r ben&ouml;tigen Sie einen Account bei disqus.com">Disqus-Shortname:</strong></td>
<td><input type="text" name="disqus_id" value="<?php echo $settings["disqus_id"];?>" size="35">
</tr>
<tr>
<td>
<td><input type="reset" value="Zurücksetzen" onclick="return confirm('Wirklich zurücksetzen?<br/>Alle Änderungen gehen verloren.')" style="width:45%;"> <input type="submit" value="OK" style="width:45%;"></td>
</tr>
</table>
<input type="hidden" name="save_settings" value="save_settings">
</form>
<br/>
<a href="index.php?action=settings">Expertenmodus</a>

<?php 
}
else{
	noperms();
}

?>




<?php }?>
