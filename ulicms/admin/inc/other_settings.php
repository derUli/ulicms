<?php
@include_once "Cache/Lite.php";

if(!is_admin()){
     echo "<p class='ulicms_error'>Zugriff verweigert</p>";
}else{

if(isset($_POST["submit"])){
  if(isset($_POST["mailer"])){
     setconfig("mailer", mysql_real_escape_string($_POST["mailer"]));
  }
  
    if(isset($_POST["cache_type"])){
     setconfig("cache_type", mysql_real_escape_string($_POST["cache_type"]));
  }
  
  if(isset($_POST["cache_enabled"]))
     deleteconfig("cache_disabled");
  else
     setconfig("cache_disabled", "disabled");

  
  if($_POST["move_from"] != "-" and $_POST["move_to"] != "-" ){
     db_query("UPDATE ".tbname("content"). " SET menu='".mysql_real_escape_string($_POST["move_to"])."' WHERE menu='".mysql_real_escape_string($_POST["move_from"])."'");
  }
}

$mailer = getconfig("mailer");
$cache_type = getconfig("cache_type");
$cache_enabled = !getconfig("cache_disabled");

$menus = getAllMenus();

?>
<h1>Sonstiges</h1>
<form id="other_settings" action="index.php?action=other_settings" method="post">
<div class="label">Mailer</div>
<div class="inputWrapper"><select name="mailer" size=1>
<option value="php-mail"<?php if($mailer == "php-mail"){ echo " selected"; }?>>PHP Mail</option>
</select>
</div>

<div class="label">Cache-Speicher</div>
<div class="inputWrapper"><select name="cache_type" size=1>
<option value="file"<?php if($cache_type === "file" or !$cache_type){ echo " selected"; }?>>Datei</option>
<option value="cache_lite"<?php if($cache_type === "cache_lite"){ echo " selected"; }?>>Cache_Lite <?php if(!class_exists("Cache_Lite")) echo " (nicht verfügbar)"?></option>
</select>
</div>

<div class="label">Cache aktiviert</div>
<div class="inputWrapper"><input type="checkbox" name="cache_enabled" value="cache_enabled" <?php if($cache_enabled) echo " checked=\"checked\""?>></div>

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
<div class="spacer"></div>
<input name="submit" type="submit" value="Einstellungen speichern"/>
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
