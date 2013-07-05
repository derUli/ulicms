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

if(getconfig("font-size") != $_REQUEST["font-size"]){
   setconfig("font-size", intval($_REQUEST["font-size"]));
}



if(getconfig("header-background-color")
 != $_REQUEST["header-background-color"]){
   setconfig("header-background-color", $_REQUEST["header-background-color"]);
}

if(getconfig("body-text-color")
 != $_REQUEST["body-text-color"]){
   setconfig("body-text-color", $_REQUEST["body-text-color"]);
}

if(getconfig("body-background-color")
 != $_REQUEST["body-background-color"]){
   setconfig("body-background-color", $_REQUEST["body-background-color"]);
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
$font_size = intval(getconfig("font-size"));

?>
<h1>Design</h1>
<form action="index.php?action=design" method="post">
<table style="width:100%;">
<tr>
<td style="width:300px;"><strong>Theme:</strong></td>
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
if($i === $zoom or ($i === 100 and $zoom === 0))
  echo " selected";
?> value="<?php echo $i;?>"><?php echo $i;?> %</option>
<?php }?>
</select>
</td>
</tr>
<tr>
<td><strong>Schriftgröße:</strong>
<td>
<select name="font-size">
<?php 
for($i=8; $i <= 96; $i+=1){
?>
<option<?php 
if($i === $font_size or ($i === 12 and $font_size === 0))
  echo " selected";
?> value="<?php echo $i;?>"><?php echo $i;?>pt</option>
<?php }?>
</select>
</td>
</tr>
<tr>
<td>
<strong>Kopfzeile Hintergrundfarbe:</strong>
</td>
<td>
<input name="header-background-color" class="color {hash:true,caps:true}" value="<?php echo getconfig("header-background-color");?>">
</td>
</tr>
<tr>
<td>
<strong>Schriftfarbe:</strong>
</td>
<td>
<input name="body-text-color" class="color {hash:true,caps:true}" value="<?php echo getconfig("body-text-color");?>">
</td>
</tr></tr>
<tr>
<td>
<strong>Hintergrundfarbe:</strong>
</td>
<td>
<input name="body-background-color" class="color {hash:true,caps:true}" value="<?php echo getconfig("body-background-color");?>">
</td>
</tr>
</table>

	<script type="text/javascript" src="scripts/jscolor.js"></script>
<p>
<input type="submit" name="submit" value="Einstellungen speichern"/>
</p>

</form>
<?php } ?>