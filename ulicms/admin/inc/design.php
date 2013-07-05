<?php 
if(!is_admin()){
echo "<p class='ulicms_error'>Zugriff verweigert</p>";
} else {

$theme = getconfig("theme");

if(isset($_REQUEST["submit"])){

  // Wenn Formular abgesendet wurde, Wert Speichern
  if($_REQUEST["theme"] !== $theme ){ // if theme auf
    $themes = getThemesList();
    if(in_array($_REQUEST["theme"], $themes)){ // if in_array theme auf
      setconfig("theme", $_REQUEST["theme"]);  
      $theme = $_REQUEST["theme"];
    } // if in_array theme zu
} // if theme zu

if($_REQUEST["default-font"] != getconfig("default-font")){
   if(!empty($_REQUEST["custom-font"]))
      $font = $_REQUEST["custom-font"];   
   else
      $font = $_REQUEST["default-font"];

   setconfig("default-font", $font);
   }
   
   
if(getconfig("zoom") != $_REQUEST["zoom"]){
setconfig("zoom", intval($_REQUEST["zoom"]));
}

} // if submit zu

$allThemes = getThemesList();

$fonts = Array();
$fonts["Times New Roman"] = "Times, Times New Roman, serif";
$fonts["Georgia"] = "Georgia";
$fonts["Sans Serif"] = "sans-serif";
$fonts["Arial"] = "arial";
$fonts["Comic Sans MS"] = "Comic Sans MS";
$fonts["Helvetica"] = "helvetica";
$fonts["Tahoma"] = "Tahoma";
$fonts["Verdana"] = "";
$fonts["Lucida Sans Unicode"] = "'Lucida Sans Unicode'";
$fonts["Trebuchet MS"] = "'Trebuchet MS'";
$fonts["Lucida Sans"] = "'Lucida Sans'";
$fonts["monospace"] = "monospace";
$fonts["Courier"] = "Courier";
$fonts["Courier New"] = "'Courier New', Courier";
$fonts["Lucida Console"] = "'Lucida Console'";
$fonts["fantasy"] = "fantasy";
$fonts["cursive"] = "cursive";

ksort($fonts);


$default_font = getconfig("default-font");
$zoom = intval(getconfig("zoom"));

?>
<h1>Design</h1>
<form action="index.php?action=design" method="post">
<table style="width:100%;">
<tr>
<td style="width:100px;"><strong>Theme:</strong></td>
<td>
<select style="width:250px;" name="theme" size=1>
<?php foreach($allThemes as $th){?>
<option value="<?php echo $th;?>"<?php 
if($th === $theme)
   echo " selected"
   ?>><?php echo $th;?></option>";
<?php }?>
</select>
</td>
</tr>
<tr>
<td><strong>Schriftart:</strong></td>
<td>
<select name="default-font" size=1>
<?php 
$font_amount = count($fonts);
$i = 1;
foreach($fonts as $key => $value){
  $selected = "";
  if($default_font === $value)
    $selected = "selected";
    
  if(!in_array($default_font, $fonts) and $i === $font_amount)
    $selected = "selected";
     
    echo "<option value=\"$value\" $selected>$key</option>";
    
    $i++;
}


?>
</select>
</td>
</tr>
<tr>
<td><strong>Zoom:</strong>
<td>
<select name="zoom">
<?php 
for($i=10; $i <= 200; $i+=10){
?>
<option<?php 
if($i === $zoom or ($i === 100 and $zoom === false))
  echo " selected";
?> value="<?php echo $i;?>"><?php echo $i;?> %</option>
<?php }?>
</select>
</td>
</tr>
</table>
<p>
<input type="submit" name="submit" value="Einstellungen speichern"/>
</p>

</form>
<?php } ?>