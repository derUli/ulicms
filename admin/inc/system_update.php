<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=50){
?>

<?php if(file_exists("../update.php")){?>
	<p><a href="../update.php">Update durchführen</a></p>
	<span style="color:red"><strong>Achtung!!!</strong> Bitte direkt nach der Durchführung des Updates die Datei <strong>update.php</strong> vom Server löschen, falls das nicht automatisch geschehen sollte.</span>
<?php 
}else{?>
Hier können Sie Ihr CMS Updaten, nachdem Sie alle Patch Dateien auf den Server geladen haben. Mehr Informationen über aktuelle Updates erhalten Sie auf www.ulicms.de
	<p>

<?php }?>

<?php 

}else{
	noperms();
}


}
?>