<?php if(defined("_SECURITY")){
     $acl = new ACL();
     if($acl -> hasPermission("expert_settings")){
         ?>

<form action="index.php?action=settings" method="post">
<input type="hidden" name="add_key" value="add_key">
<strong><?php echo TRANSLATION_OPTION;
        ?></strong><br/>
<input type="text" style="width:300px;" name="name" value="">
<br/><br/>
<strong><?php echo TRANSLATION_VALUE;
        ?></strong><br/>
<textarea style="width:300px; height:150px;" style="width:300px;" name="value"></textarea>

<br/><br/>
<input type="submit" value="<?php echo TRANSLATION_CREATE_OPTION;
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
         }
    else{
         noperms();
         }
    
     ?>




<?php }
?>
