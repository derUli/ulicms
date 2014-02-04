<?php if(defined("_SECURITY")){
     if(is_admin() or $acl -> hasPermission("users")){
        
         $query = db_query("SELECT * FROM " . tbname("admins") . " ORDER BY id", $connection);
         if(db_num_rows($query)){
             ?>
<form action="index.php?action=admins" method="post">
<input type="hidden" name="add_admin" value="add_admin">
<strong data-tooltip="Dieser Name wird zur Anmeldung im Administrationsbereich benötigt...">Benutzername:</strong><br/>
<input type="text" style="width:300px;" name="admin_username" value="">
<br/><br/>
<strong>Nachname:</strong><br/>
<input type="text" style="width:300px;" name="admin_lastname" value="">
<br/><br/>
<strong>Vorname:</strong><br/>
<input type="text" style="width:300px;" name="admin_firstname" value=""><br/><br/>
<strong>Email:</strong><br/>
<input type="text" style="width:300px;" name="admin_email" value=""><br/><br/>
<strong data-tooltip="Das Passwort des neuen Administrators. Es wird bei der Eingabe im Klartext angezeigt...">Passwort:</strong><br/>
<input type="text" style="width:300px;" name="admin_password" value=""><br/><br/>
<input type="checkbox" id="send_mail" name="send_mail" value="sendmail"> <label for="send_mail">Die Zugangsdaten per E-Mail senden</label>
<br/>
<br/>
<input type="submit" value="Benutzer anlegen">
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