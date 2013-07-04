<?php 
if(!is_admin()){
echo "<p class='ulicms_error'>Zugriff verweigert</p>";
} else {

$theme = getconfig("theme");

// Wenn Formular abgesendet wurde, Wert Speichern
if($_REQUEST["theme"] !== $theme ){
   $themes = getThemesList();
   if(in_array($_REQUEST["theme"], $themes)){
      setconfig("theme", $_REQUEST["theme"]);  
   }
}


?>
<h1>Design</h1>
<form action="index.php?action=design" method="post">

<input type="submit" value="Einstellungen speichern"/>

</form>
<?php } ?>