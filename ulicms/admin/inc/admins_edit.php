<?php if(defined("_SECURITY")){
include_once ULICMS_ROOT.DIRECTORY_SEPERATOR."lib".DIRECTORY_SEPERATOR."string_functions.php";
    
     if($acl->hasPermission("group") or is_admin() or
         ($_GET["admin"] == $_SESSION["login_id"])){
        
         $admin = intval($_GET["admin"]);
        
         $query = db_query("SELECT * FROM " . tbname("admins") . " WHERE id='$admin'");
         while($row = db_fetch_object($query)){
             ?>

<form action="index.php?action=admins" name="userdata_form" method="post" enctype="multipart/form-data">
<input type="hidden" name="edit_admin" value="edit_admin">
<input type="hidden" name="id" value="<?php echo $row -> id;
             ?>">
<strong data-tooltip="Dieser Name wird zur Anmeldung benötigt. Er ist nicht änderbar.">Benutzername:</strong><br/>
<input type="text" style="width:300px;" name="admin_username" value="<?php echo $row -> username;
             ?>" readonly="readonly">
<br/><br/>
<?php if(file_exists("../content/avatars/" . $row -> avatar_file) and !empty($row -> avatar_file)){
                 ?>
<img src='../content/avatars/<?php echo $row -> avatar_file?>' alt="Avatarbild">
<br/>
<?php
                 }
             ?>
Avatar hochladen:<br/>
<input type="file" name="avatar_upload" accept="image/jpeg"><br>
<small>Nur JPEG-Grafiken werden akzeptiert</small>

<br/>                             
<br/>
<strong>Nachname:</strong><br/>
<input type="text" style="width:300px;" name="admin_lastname" value="<?php echo $row -> lastname;
             ?>">
<br/><br/>
<strong>Vorname:</strong><br/>
<input type="text" style="width:300px;" name="admin_firstname" value="<?php echo $row -> firstname;
             ?>"><br/><br/>
<strong>Email:</strong><br/>
<input type="text" style="width:300px;" name="admin_email" value="<?php echo $row -> email;
             ?>"><br/><br/>
<strong>neues Passwort:</strong><br/>
<input type="text" style="width:300px;" name="admin_password" value=""> <br/>
<?php 
$acl = new ACL();
if($acl->hasPermission("users")){

$allGroups = $acl->getAllGroups();
asort($allGroups);
?>
<br>
<strong>ACL-Gruppe:</strong>
<br/>
<select name="group_id">
<option value="-" <?php if($row -> group_id === null){ echo "selected"; }?>>[Keine]</option>
<?php foreach($allGroups as $key => $value){?>
<option value="<?php echo $key;?>" <?php 
if(intval($row -> group_id) == $key)
{ echo "selected"; 

}
?>><?php echo  real_htmlspecialchars($value)?></option>
<?php }?>
</select>

<br/>
<br/>
<strong>Benutzergruppe:</strong><br/>

<select name="admin_rechte" size=1>
<option value="50" <?php if($row -> group == 50) echo "selected";
                 ?>>Admin</option>
<option value="40" <?php if($row -> group == 40) echo "selected";
                 ?>>Redakteur</option>
<option value="30" <?php if($row -> group == 30) echo "selected";
                 ?>>Autor</option>
<option value="20" <?php if($row -> group == 20) echo "selected";
                 ?>>Mitarbeiter</option>
<option value="10" <?php if($row -> group == 10) echo "selected";
                 ?>>Gast</option>
<option value="0" <?php if($row -> group == 0) echo "selected";
                 ?>>Gesperrter Nutzer</option>
</select>
<br>
<?php }else{
                 ?>
<input type="hidden" name="admin_rechte" value="<?php echo $row -> group?>">

<input type="hidden" name="group_id" value=<?php if(!$_SESSION["group_id"])
   echo "-";
else
   echo $_SESSION["group_id"];
?>">
<?php }
             ?>
<br/>



<strong>ICQ:</strong>   <br/>
<input type="text" name="icq_id" value="<?php echo $row -> icq_id?>">

<br/><br/>
<strong>Skype:</strong>   <br/>
<input type="text" name="skype_id" value="<?php echo $row -> skype_id?>">

<br/>   
<br/>
<strong>Über Mich:</strong><br/>
<textarea rows=10 cols=50 name="about_me"><?php echo htmlspecialchars($row -> about_me)?></textarea>
<br/> <br/>
<input type="submit" value="OK">
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
