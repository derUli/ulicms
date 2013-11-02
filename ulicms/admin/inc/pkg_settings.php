<?php
if(!is_admin()){
     echo "<p class='ulicms_error'>Zugriff verweigert</p>";
     }else{
    
     // Wenn Formular abgesendet wurde, Wert Speichern
    if(isset($_REQUEST["pkg_src"])){
         $new_pkg_src = trim($_REQUEST["pkg_src"]);
         if(!endsWith($new_pkg_src, "/"))
             $new_pkg_src .= "/";
        
         if($new_pkg_src == "/"){
             deleteconfig("pkg_src");
             }
        else{
             $new_pkg_src = db_real_escape_string($new_pkg_src);
             setconfig("pkg_src", $new_pkg_src);
             }
         }
    
     $default_pkg_src = "http://www.ulicms.de/packages/{version}/";
    
     $version = new ulicms_version();
     $version = $version -> getInternalVersion();
     $version = implode(".", $version);
    
     $local_pkg_dir = "../packages/";
     $local_pkg_dir_value = "../packages/";
     $pkg_src = getconfig("pkg_src");
    
     $is_other = ($pkg_src !== $default_pkg_src and $pkg_src !== $local_pkg_dir and $pkg_src !== $local_pkg_dir_value);
    
    
     include_once "../lib/file_get_contents_wrapper.php";
     ?>
<h1>Paketquelle</h1>
<form id="pkg_settings" action="index.php?action=pkg_settings" method="post">
<input type="radio" name="radioButtonSRC"<?php if($pkg_src === $default_pkg_src) echo " checked";
     ?> onclick="$('#sonstigePaketQuelle').slideUp(); $('#pkg_src').val('<?php echo $default_pkg_src?>');"> Offizielle Paketquelle [www.ulicms.de]<br>

<input type="radio" name="radioButtonSRC" <?php if($pkg_src === $local_pkg_dir or $pkg_src === $local_pkg_dir_value) echo " checked";
     ?>  onclick="$('#sonstigePaketQuelle').slideUp(); $('#pkg_src').val('<?php echo $local_pkg_dir_value?>');"> Aus dem Dateisystem<br>


<input type="radio" name="radioButtonSRC" <?php if($is_other) echo " checked";
     ?> onclick="$('#sonstigePaketQuelle').slideDown();"> Andere Paketquelle [URL]<br>
<div id="sonstigePaketQuelle" <?php
     if($is_other)
         echo 'style="display:block"';
     else
         echo 'style="display:none"';
     ?>>
<input style="width:400px" type="text" id="pkg_src" name="pkg_src" value="<?php echo htmlspecialchars($pkg_src);
     ?>">
</div>

<br/>
<input type="submit" value="Einstellungen speichern"/>

<?php 
if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }?>
</form>

<script type="text/javascript">
$("#pkg_settings").ajaxForm({beforeSubmit: function(e){
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
