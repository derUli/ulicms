<?php
if(!is_admin()){
     echo "<p class='ulicms_error'>Zugriff verweigert</p>";
     }else{
     $theme = getconfig("theme");
    
     if(isset($_REQUEST["submit"])){
        
         // Wenn Formular abgesendet wurde, Wert Speichern
        if($_REQUEST["theme"] !== $theme){ // if theme auf
             $themes = getThemesList();
             if(in_array($_REQUEST["theme"], $themes)){ // if in_array theme auf
                 setconfig("theme", db_escape($_REQUEST["theme"]));
                 $theme = $_REQUEST["theme"];
                 } // if in_array theme zu
             } // if theme zu
        
         if($_REQUEST["default-font"] != getconfig("default-font")){
             if(!empty($_REQUEST["custom-font"]))
                 $font = $_REQUEST["custom-font"];
             else
                 $font = $_REQUEST["default-font"];
            
             $font = db_escape($font);
            
             setconfig("default-font", $font);
             }
        
        
             setconfig("zoom", intval($_REQUEST["zoom"]));
     
        
             setconfig("font-size", intval($_REQUEST["font-size"]));
  
        
        
        setconfig("ckeditor_skin", db_escape($_REQUEST["ckeditor_skin"]));
        
         setconfig("backend_style", db_escape($_REQUEST["backend_style"]));
        
        
         if(getconfig("header-background-color")
                 != $_REQUEST["header-background-color"]){
             setconfig("header-background-color", db_escape($_REQUEST["header-background-color"]));
             }
        
         if(getconfig("body-text-color")
                 != $_REQUEST["body-text-color"]){
             setconfig("body-text-color",
                 db_escape($_REQUEST["body-text-color"]));
             }
        
         if(getconfig("title_format") != $_REQUEST["title_format"])
             setconfig("title_format",
                 db_escape($_REQUEST["title_format"]));
        
         if(getconfig("body-background-color")
                 != $_REQUEST["body-background-color"]){
             setconfig("body-background-color",
                 db_escape($_REQUEST["body-background-color"]));
             }
        
        
         } // if submit zu
    
     $allThemes = getThemesList();
     include_once "inc/fonts.php";
     $fonts = getFontFamilys();
    
     $backend_style = getconfig("backend_style");
     if(!$backend_style)
         $backend_style = "green";
    
    
     $default_font = getconfig("default-font");
     $title_format = htmlspecialchars(getconfig("title_format"), ENT_QUOTES, "UTF-8");
     $zoom = intval(getconfig("zoom"));
     $font_size = intval(getconfig("font-size"));
     $ckeditor_skin = getconfig("ckeditor_skin");
    
     ?>
<style type="text/css">
input[type="text"], select,
input.color{
width:200px;
}
</style>
<h1>Design</h1>
<form id="designForm" action="index.php?action=design" method="post">
<table style="width:100%;">
<tr>
<td style="width:300px;">
<strong>Title</strong>
</td>
<td><input type="text" name="title_format" value="<?php echo $title_format;
     ?>"></td>
</tr>
<tr>
<td><strong>Frontend Design:</strong></td>
<td>
<select name="theme" size=1>
<?php foreach($allThemes as $th){
         ?>
<option value="<?php echo $th;
         ?>"<?php
         if($th === $theme)
             echo " selected"
             ?>><?php echo $th;
         ?></option>";
<?php }
     ?>
</select>
</td>
</tr>
<tr>
    <td>
        <strong>Backend Design:</strong>
    </td>
    <td>
<select name="backend_style" size=1>
<option value="green"<?php
     if($backend_style === "green")
         echo " selected"
         ?>>Grün</option>
<option value="black"<?php
         if($backend_style === "black")
             echo " selected"
             ?>>Schwarz</option>
</select>

    </td>
</tr>
<tr>
    <td>
        <strong>Editor Skin:</strong>
    </td>
    <td>
<select name="ckeditor_skin" size=1>
<option value="moono"<?php
     if($ckeditor_skin === "moono")
         echo " selected"
         ?>>Moono</option>
         <!--
<option value="kama"<?php
         if($ckeditor_skin === "kama")
             echo " selected"
             ?>>Kama</option>
             -->
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
             echo '<optgroup style="font-family:' . $value . '; font-size:1.2em;">';
             echo "<option value=\"$value\" $selected>$key</option>";
             echo '</optgroup>';
            
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
         for($i = 10; $i <= 200; $i += 10){
             ?>
<option<?php
             if($i === $zoom or ($i === 100 and $zoom === 0))
                 echo " selected";
             ?> value="<?php echo $i;
             ?>"><?php echo $i;
             ?> %</option>
<?php }
         ?>
</select>
</td>
</tr>
<tr>
<td><strong>Schriftgröße:</strong>
<td>
<select name="font-size">
<?php
         for($i = 8; $i <= 96; $i += 1){
             ?>
<option<?php
             if($i === $font_size or ($i === 12 and $font_size === 0))
                 echo " selected";
             ?> value="<?php echo $i;
             ?>"><?php echo $i;
             ?>pt</option>
<?php }
         ?>
</select>
</td>
</tr>
<tr>
<td>
<strong>Kopfzeile Hintergrundfarbe:</strong>
</td>
<td>
<input name="header-background-color" class="color {hash:true,caps:true}" value="<?php echo getconfig("header-background-color");
         ?>">
</td>
</tr>
<tr>
<td>
<strong>Schriftfarbe:</strong>
</td>
<td>
<input name="body-text-color" class="color {hash:true,caps:true}" value="<?php echo getconfig("body-text-color");
         ?>">
</td>
</tr></tr>
<tr>
<td>
<strong>Hintergrundfarbe:</strong>
</td>
<td>
<input name="body-background-color" class="color {hash:true,caps:true}" value="<?php echo getconfig("body-background-color");
         ?>">
</td>
</tr>
</table>
<p>
<input type="submit" name="submit" value="Einstellungen speichern"/>
</p>

<?php
         if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
             ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
         ?>
</form>
<script type="text/javascript">
$("#designForm").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
  }
  

}); 

</script>
<?php }
    ?>