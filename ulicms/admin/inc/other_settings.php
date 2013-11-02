<?php
@include_once "Cache/Lite.php";

if(!is_admin()){
     echo "<p class='ulicms_error'>Zugriff verweigert</p>";
}else{

if(isset($_POST["submit"])){
  if(isset($_POST["mailer"])){
     setconfig("mailer", db_real_escape_string($_POST["mailer"]));
  }
  
    if(isset($_POST["cache_period"])){  
       setconfig("cache_period", intval($_POST["cache_period"]) * 60);
    }
  
    if(isset($_POST["cache_type"])){
     setconfig("cache_type", db_real_escape_string($_POST["cache_type"]));
  }
  
  if(isset($_POST["override_shortcuts"])){
     setconfig("override_shortcuts", db_real_escape_string($_POST["override_shortcuts"]));
       
  }
  
  if(isset($_POST["cache_enabled"]))
     deleteconfig("cache_disabled");
  else
     setconfig("cache_disabled", "disabled");

  
  if($_POST["move_from"] != "-" and $_POST["move_to"] != "-" ){
     db_query("UPDATE ".tbname("content"). " SET menu='".db_real_escape_string($_POST["move_to"])."' WHERE menu='".db_real_escape_string($_POST["move_from"])."'");
  }
}

$mailer = getconfig("mailer");
$cache_type = getconfig("cache_type");
$cache_enabled = !getconfig("cache_disabled");
$cache_period = round(getconfig("cache_period") / 60);
$override_shortcuts = getconfig("override_shortcuts");
$menus = getAllMenus();

?>
<h1>Sonstiges</h1>
<form id="other_settings" action="index.php?action=other_settings" method="post">
<div class="label">Mailer</div>
<div class="inputWrapper"><select name="mailer" size=1>
<option value="php-mail"<?php if($mailer == "php-mail"){ echo " selected"; }?>>PHP Mail</option>
</select>
</div>
<div class="seperator"></div>

<div class="label">Cache aktiviert</div>
<div class="inputWrapper"><input type="checkbox" name="cache_enabled" value="cache_enabled" <?php if($cache_enabled) echo " checked=\"checked\"";?>></div>
<div class="label">Cache Gültigkeitsdauer</div>
<div class="inputWrapper"><input type="number" name="cache_period" min=1 max=20160 value="<?php echo $cache_period;?>"> Minuten</div>

<div class="label">Cache-Speicher</div>
<div class="inputWrapper"><select name="cache_type" size=1>
<option value="file"<?php if($cache_type === "file" or !$cache_type){ echo " selected"; }?>>Datei</option>
<option value="cache_lite"<?php if($cache_type === "cache_lite"){ echo " selected"; }?>>Cache_Lite <?php if(!class_exists("Cache_Lite")) echo " (nicht verfügbar)"?></option>
</select>
</div>

<div class="seperator"></div>

<div class="label">Shortcuts ersetzen</div>
<div class="inputWrapper">
<select name="override_shortcuts" size=1>
<option value="off" <?php if($override_shortcuts == "off" or !$override_shortcuts) echo " selected=\"selected\""?>>Aus</option>
<option value="frontend" <?php if($override_shortcuts == "frontend") echo " selected=\"selected\""?>>Nur im Frontend</option>
<option value="backend" <?php if($override_shortcuts == "backend") echo " selected=\"selected\""?>>Nur im Backend</option>
<option value="on" <?php if($override_shortcuts == "on") echo " selected=\"selected\""?>>Im Frontend und Backend</option>
</select>

<p>Standard-Tastenkombinationen des Browsers werden ersetzt, so dass Sie z.B. durch Drücken von Strg+S ebenfalls speichern können</p
</div>




<div class="seperator"></div>

<h2>Menüeinträge verschieben</h2>
<p>Verschiebe alle Menüeinträge von <select name="move_from" size="1">
                                    <option value="-" selected>-</option>
                                    <?php foreach ($menus as $menu){
                                    ?>
                                    <option value="<?php echo $menu?>"><?php echo $menu?></option>
                                    <?php
                                    }?>
                                    </select> nach <select name="move_to" size="1">
                                    <option value="-" selected>-</option>
                                    <?php foreach ($menus as $menu){
                                    ?>
                                    <option value="<?php echo $menu?>"><?php echo $menu?></option>
                                    <?php
                                    }?>
                                    </select> 
                                    </p>
<div class="seperator"></div>

<p><a href="index.php?action=settings">Experteneinstellungen</a></p>


<input name="submit" type="submit" value="Einstellungen speichern"/>

<?php 
if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }?>
</form>

<script type="text/javascript">
$("#other_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
  }
  

}); 

</script>

<?php } ?> 
