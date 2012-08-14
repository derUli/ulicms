<?php 
if(getconfig("visitors_can_register")=="off")
	die("Diese Funktion ist deaktiviert");

$errors = false;
if(isset($_POST["register_user"])){
    
    if(empty($_POST["admin_username"]) or empty($_POST["admin_password"]) or empty($_POST["admin_firstname"]) or empty($_POST["admin_lastname"])){
      echo "<p style='color:red;'>Bitte füllen Sie alle Felder aus.</p>";
      
    }
    else if(user_exists($_POST["admin_username"])){
      echo "<p style='color:red;'>Dieser Benutzername ist leider schon vergeben. </p>";
    }
    else{
      adduser($_POST["admin_username"],
      $_POST["admin_lastname"],
      $_POST["admin_firstname"],
      $_POST["admin_email"], $_POST["admin_password"], 10);
      echo "<p style='color:green;'>Registrierung erfolgreich!</p>";
      echo "<p><a href='index.php'>Hier gehts weiter</a></p>";
    }
}

?>
<h1>Registrierung</h1>
<form action="index.php?register=register" method="post">
<input type="hidden" name="register_user" value="add_admin">
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

<br/>

<input type="submit" value="Datensatz hinzufügen">
</form>