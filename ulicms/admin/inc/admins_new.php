<?php if(defined("_SECURITY")){
     if(is_admin() or $acl -> hasPermission("users")){
        
         $query = db_query("SELECT * FROM " . tbname("users") . " ORDER BY id", $connection);
         if(db_num_rows($query)){
             ?>
<form action="index.php?action=admins" method="post">
<input type="hidden" name="add_admin" value="add_admin">
<strong><?php echo TRANSLATION_USERNAME;
             ?></strong><br/>
<input type="text" required="true" style="width:300px;" name="admin_username" value="">
<br/><br/>
<strong><?php echo TRANSLATION_LASTNAME;
             ?></strong><br/>
<input type="text" style="width:300px;" name="admin_lastname" value="">
<br/><br/>
<strong><?php echo TRANSLATION_FIRSTNAME;
             ?></strong><br/>
<input type="text" style="width:300px;" name="admin_firstname" value=""><br/><br/>
<strong><?php echo TRANSLATION_EMAIL;
             ?></strong><br/>
<input type="email" style="width:300px;" name="admin_email" value=""><br/><br/>
<strong><?php echo TRANSLATION_PASSWORD;
             ?></strong><br/>
<input type="text" required="true" style="width:300px;" name="admin_password" value=""><br/><br/>
<input type="checkbox" id="send_mail" name="send_mail" value="sendmail"> <label for="send_mail"><?php echo TRANSLATION_SEND_LOGINDATA_BY_MAIL;
             ?></label>
<br/>
<br/>
<input type="submit" value="<?php echo TRANSLATION_CREATE_USER;
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


<?php }
?>