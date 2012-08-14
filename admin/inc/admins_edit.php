<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=50){

$admin=intval($_GET["admin"]);
$query=mysql_query("SELECT * FROM ".tbname("admins")." WHERE id='$admin'");
while($row=mysql_fetch_object($query)){
?>

<form action="index.php?action=admins" name="userdata_form" method="post">
<input type="hidden" name="edit_admin" value="edit_admin">
<input type="hidden" name="id" value="<?php echo $row->id;?>">
<strong data-tooltip="Dieser Name wird zur Anmeldung benötigt. Er ist nicht änderbar.">Benutzername:</strong><br/>
<input type="text" style="width:300px;" name="admin_username" value="<?php echo $row->username;?>" readonly="readonly">
<br/><br/>
<strong>Nachname:</strong><br/>
<input type="text" style="width:300px;" name="admin_lastname" value="<?php echo $row->lastname;?>">
<br/><br/>
<strong>Vorname:</strong><br/>
<input type="text" style="width:300px;" name="admin_firstname" value="<?php echo $row->firstname;?>"><br/><br/>
<strong>Email:</strong><br/>
<input type="text" style="width:300px;" name="admin_email" value="<?php echo $row->email;?>"><br/><br/>
<strong data-tooltip="Das Passwort des Administrators als MD5-Hash (Einweg-Verschlüsselung)...">Passwort:</strong><br/>
<input type="text" style="width:300px;" name="admin_password" value="<?php echo $row->password;?>"> <input type="button" value="Passwort verschlüsseln" onclick="document.userdata_form.admin_password.value = MD5 (document.userdata_form.admin_password.value)"><br/>
<br/>
<strong data-tooltip="Was darf der Benutzer? Weitere Informationen dazu finden Sie in der Online-hilfe.">Benutzergruppe:</strong><br/>
<select name="admin_rechte" size=1>
<option value="50" <?php if($row->group==50) echo "selected";?>>Admin</option>
<option value="40" <?php if($row->group==40) echo "selected";?>>Redakteur</option>
<option value="30" <?php if($row->group==30) echo "selected";?>>Autor</option>
<option value="20" <?php if($row->group==20) echo "selected";?>>Mitarbeiter</option>
<option value="10" <?php if($row->group==10) echo "selected";?>>Gast</option>
</select>
<br/>

<br/><br/>
<input type="submit" value="OK">
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




<?php }?>
