<?php
if(getconfig("visitors_can_register") == "off")
     die("Diese Funktion ist deaktiviert");

$errors = false;
if(isset($_POST["register_user"])){
    
     if(empty($_POST["admin_username"]) or empty($_POST["admin_password"]) or empty($_POST["admin_firstname"]) or empty($_POST["admin_lastname"])){
         echo "<p style='color:red;'>Bitte füllen Sie alle Felder aus.</p>";
        
         }
    else if(user_exists($_POST["admin_username"])){
         echo "<p style='color:red;'>Dieser Benutzername ist leider schon vergeben. </p>";
         }
    else if($_POST["admin_password"] != $_POST["admin_password_repeat"]){
         echo "<p style='color:red;'>Die Wiederholung stimmt nicht mit dem Passwort überein.</p>";
         }
    
    else{
         $registered_user_default_level = getconfig("registered_user_default_level");
         if($registered_user_default_level === false){
             $registered_user_default_level = 10;
             }
         adduser($_POST["admin_username"],
             $_POST["admin_lastname"],
             $_POST["admin_firstname"],
             $_POST["admin_email"], $_POST["admin_password"],
             $registered_user_default_level);
         echo "<p style='color:green;'>Registrierung erfolgreich!</p>";
         if(!empty($_REQUEST["go"])){
             $go = htmlspecialchars($_REQUEST["go"]);
             }else{
             $go = "index.php";
             }
         echo "<p><a href='$go'>Hier gehts weiter</a></p>";
         }
     }

?>
<?php add_hook("before_register_form_title");
?>
<h1>Registrierung</h1>
<?php add_hook("before_register_form");
?>
<form action="index.php?register=register" method="post">
<input type="hidden" name="register_user" value="add_admin">
<?php if(!empty($_REQUEST["go"])){
     ?>
<input type="hidden" name="go" value='<?php
     echo htmlspecialchars($_REQUEST["go"])?>'>
<?php }
?>
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
<strong data-tooltip="Das Passwort des neuen Benutzers. Es wird Ihnen nach der Registrierung per E-Mail zugeschickt">Passwort:</strong><br/>
<input type="password" style="width:300px;" name="admin_password" value=""><br/><br/>

<strong>Passwort wiederholen:</strong><br/>
<input type="password" style="width:300px;" name="admin_password_repeat" value=""><br/><br/>
<?php add_hook("register_form_field");
?>
<br/>

<?php
if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
    ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
?>
<input type="submit" value="Registrieren">
</form>

<?php add_hook("after_register_form");
?>
