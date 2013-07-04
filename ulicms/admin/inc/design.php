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
      $theme = $_REQUEST["theme"];
   }
}

$allThemes = getThemesList();


?>
<h1>Design</h1>
<form action="index.php?action=design" method="post">
<table style="width:100%;">
<tr>
<td style="width:100px;"><strong>Design:</strong></td>
<td>
<select style="width:250px;" name="theme" size=1>
<?php foreach($allThemes as $th){?>
<option value="<?php echo $th;?>"<?php 
if($th === $theme)
   echo " selected"?>><?php echo $th;?></option>";
<?php }?>
</select>
</td>
</tr>
</table>
<p>
<input type="submit" value="Einstellungen speichern"/>
</p>

</form>
<?php } ?>