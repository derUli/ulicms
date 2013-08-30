<?php
if(!is_admin()){
     echo "<p class='ulicms_error'>Zugriff verweigert</p>";
}else{

if(isset($_POST["submit"])){
  if(isset($_POST["mailer"])){
     setconfig("mailer", mysql_real_escape_string($_POST["mailer"]));
  }
  
  if($_POST["move_from"] != "-" and $_POST["move_to"] != "-" ){
     db_query("UPDATE ".tbname("content"). " SET menu='".mysql_real_escape_string($_POST["move_to"])."' WHERE menu='".mysql_real_escape_string($_POST["move_from"])."'");
  }
}

$mailer = getconfig("mailer");

$menus = getAllMenus();

?>
<h1>Sonstiges</h1>
<form id="other_settings" action="index.php?action=other_settings" method="post">
<div class="label">Mailer:</div>
<div class="inputWrapper"><select name="mailer" size=1>
<option value="php-mail"<?php if($mailer == "php-mail"){ echo " selected"; }?>>PHP Mail</option>
</select>
</div>
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
