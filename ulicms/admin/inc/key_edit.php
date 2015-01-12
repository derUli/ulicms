<?php
$acl = new ACL();

if(defined("_SECURITY")){
     if($acl -> hasPermission("expert_settings")){
         $key = intval($_GET["key"]);
         $query = db_query("SELECT * FROM " . tbname("settings") . " WHERE id='$key'");
         while($row = db_fetch_object($query)){
             ?>

<form action="index.php?action=settings" method="post">

<input type="hidden" name="id" value="<?php echo $row -> id;
             ?>">
<input type="hidden" name="edit_key" value="edit_key">
<strong><?php echo TRANSLATION_OPTION;
            ?></strong><br/>
<input type="text" style="width:300px;" name="name" value="<?php echo htmlspecialchars($row -> name, ENT_QUOTES, "UTF-8");
             ?>" readonly="readonly">
<br/><br/>
<strong><?php echo TRANSLATION_VALUE;
            ?></strong><br/>
<textarea style="width:300px; height:150px;" style="width:300px;" name="value"><?php echo htmlspecialchars($row -> value, ENT_QUOTES, "UTF-8");
             ?></textarea>

<br/><br/>
<input type="submit" value="<?php echo TRANSLATION_SAVE_CHANGES;
            ?>">

<?php
             if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
                 ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
             ?>
</form>


<?php
             break;
             }
         ?>
<?php
         }
    else{
         noperms();
         }
    
     ?>




<?php }
?>
