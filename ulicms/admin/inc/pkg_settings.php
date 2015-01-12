<?php
$acl = new ACL();
if(!$acl -> hasPermission("pkg_settings")){
     noperms();
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
             $new_pkg_src = db_escape($new_pkg_src);
             setconfig("pkg_src", $new_pkg_src);
             }
         }
    
     $default_pkg_src = "http://packages.ulicms.de/{version}/";
    
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
    <fieldset>
<input type="radio" id="pkgsrc1" name="radioButtonSRC"<?php if($pkg_src === $default_pkg_src) echo " checked";
     ?> onclick="$('#sonstigePaketQuelle').slideUp(); $('#pkg_src').val('<?php echo $default_pkg_src?>');"> <label for="pkgsrc1"><?php echo TRANSLATION_OFFICIAL_PACKAGE_SOURCE;
    ?></label><br>

<input type="radio" id="pkgsrc2" name="radioButtonSRC" <?php if($pkg_src === $local_pkg_dir or $pkg_src === $local_pkg_dir_value) echo " checked";
     ?>  onclick="$('#sonstigePaketQuelle').slideUp(); $('#pkg_src').val('<?php echo $local_pkg_dir_value?>');"> <label for="pkgsrc2"><?php echo TRANSLATION_FROM_FILESYSTEM;
    ?></label><br>


<input type="radio" id="pkgsrc3" name="radioButtonSRC" <?php if($is_other) echo " checked";
     ?> onclick="$('#sonstigePaketQuelle').slideDown();"> <label for="pkgsrc3"><?php echo TRANSLATION_OTHER_PACKAGE_SOURCE;
    ?></label><br>
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
<input type="submit" value="<?php echo TRANSLATION_SAVE_CHANGES;
    ?>"/>

<?php
     if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
         ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
     ?>
 </fieldset>
</form>

<script type="text/javascript">
$("#pkg_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\"><?php echo TRANSLATION_CHANGES_WAS_SAVED;
    ?></span>");
  }
  

}); 

</script>

<?php }
?>
