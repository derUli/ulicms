<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=50){
?>

<h2>Templates</h2>
<?php

if(!empty($_GET["save"])){
if($_GET["save"]=="true"){
echo "<p>Die Template wurde gespeichert.</p>";
}else{
echo "<p>Die Template konnte nicht gespeichert werden. Möglicherweise ein Problem mit den Dateirechten auf dem Server?</p>";
}
}
else if(empty($_GET["edit"])){
?>
<p>Hier können Sie das Aussehen Ihrer Website durch Templates anpassen. Bitte vorsichtig beim Bearbeiten sein, wegen des enthaltenen PHP-Codes. Am Besten sollte diese Aufgabe von einem Profi übernommen werden.</p>
<strong>Bitte wählen Sie ein Template aus:</strong><br/>
<p><a href="index.php?action=templates&edit=oben.php">Oben</a></p>
<p><a href="index.php?action=templates&edit=unten.php">Unten</a></p>
<!-- <p><a href="index.php?action=templates&edit=news.txt">News</a></p> !-->
<p><a href="index.php?action=templates&edit=maintenance.php">Wartungsmodus</a></p>
<p><a href="index.php?action=templates&edit=style.css">Stylesheet</a></p>

<?php 
if(file_exists("../templates/403.php")){
?>
<p><a href="index.php?action=templates&edit=403.php">403 Fehlerseite</a></p>
<?php 
}
?>

<?php 
if(file_exists("../templates/404.php")){
?>
<p><a href="index.php?action=templates&edit=404.php">404 Fehlerseite</a></p>
<?php 
}
?>

 <?php 
         if(file_exists("../templates/functions.php")){
     ?>
      <p><a href="index.php?action=templates&edit=functions.php">Functions</a></p>
     <?php 
     }
     ?>


<?php }else if (!empty($_GET["edit"])){
	$edit=basename($_GET["edit"]);
	$template_file="../templates/$edit";

	if(is_file($template_file)){

		if(!is_writable($template_file)&&file_exists($template_file)){
			echo "<p>Die gewählte Template konnte nicht geöffnet werden. Wenn Sie der Inhaber dieser Seite sind, probieren Sie die Datei-Rechte auf dem FTP-Server auf 0777 zu setzen. Wenn nicht, wenden Sie sich bitte an Ihren Administrator.</p>";
		}else{
		$template_content = file_get_contents($template_file);

?>
<form action="index.php?action=templates" method="post">
<style type="text/css">
.CodeMirror {
  border: 1px solid #eee;
  height: auto;
  overflow:hidden;
}
.CodeMirror-scroll {
  overflow-y: hidden;
  overflow-x: auto;
}
</style>
<textarea id="code" name="code" cols=80 rows=20><?php
  echo htmlspecialchars($template_content);?></textarea>
 <script type="text/javascript">
      var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
        lineNumbers: true,
        matchBrackets: true,
		
        mode: "<?php switch(file_extension($edit)){
		case "php":
		echo "application/x-httpd-php";
		break;
		break;
		case "css":
		echo "text/css";
		break;
		case "txt":
		echo "application/x-httpd-php";
		break;
		}
		?>",
		
        indentUnit: 0,
        indentWithTabs: false,
        enterMode: "keep",
        tabMode: "shift"
      });
    </script>
    <br/><br/>
    <input type="hidden" name="save_template" value="<?php echo htmlspecialchars($edit);?>">
    <input type="submit" value="Änderungen Speichern">
</form>

<?php



}

}

?>




<?php }
?>

<?php 
}
else{
noperms();
}

?>




<?php }?>
