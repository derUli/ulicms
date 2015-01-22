<?php if(defined("_SECURITY")){
     include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";
     $acl = new ACL();
    
     if(($acl -> hasPermission("users") or is_admin()) or
             ($_GET["admin"] == $_SESSION["login_id"])){
        
         $admin = intval($_GET["admin"]);
        
         $query = db_query("SELECT * FROM " . tbname("users") . " WHERE id='$admin'");
         while($row = db_fetch_object($query)){
             ?>

<form action="index.php?action=admins" name="userdata_form" method="post" enctype="multipart/form-data">
<input type="hidden" name="edit_admin" value="edit_admin">
<input type="hidden" name="id" value="<?php echo $row -> id;
             ?>">
<strong><?php echo TRANSLATION_USERNAME;
             ?></strong><br/>
<input type="text"  name="admin_username" value="<?php echo $row -> username;
             ?>" <?php if(!$acl -> hasPermission("users")){
                 ?>readonly="readonly"<?php }
             ?>>
<br/><br/>
<?php if(file_exists("../content/avatars/" . $row -> avatar_file) and !empty($row -> avatar_file)){
                 ?>
<img src='../content/avatars/<?php echo $row -> avatar_file?>' alt="Avatarbild">
<br/>
<?php
                 }
             ?>
<?php echo TRANSLATION_UPLOAD_AVATAR;
             ?><br/>
<input type="file" name="avatar_upload" accept="image/jpeg"><br>
<small><?php echo TRANSLATION_ONLY_JPEG;
             ?></small>

<br/>                             
<br/>
<strong><?php echo TRANSLATION_LASTNAME;
             ?></strong><br/>
<input type="text"  name="admin_lastname" value="<?php echo $row -> lastname;
             ?>">
<br/><br/>
<strong><?php echo TRANSLATION_FIRSTNAME;
             ?></strong><br/>
<input type="text"  required="true" name="admin_firstname" value="<?php echo $row -> firstname;
             ?>"><br/><br/>
<strong><?php echo TRANSLATION_EMAIL;
             ?></strong><br/>
<input type="email"  name="admin_email" value="<?php echo $row -> email;
             ?>"><br/><br/>
<strong><?php echo TRANSLATION_NEW_PASSWORD;
             ?></strong><br/>
<input type="text"  name="admin_password" value=""> <br/>
<?php
             $acl = new ACL();
             if($acl -> hasPermission("users")){
                
                 $allGroups = $acl -> getAllGroups();
                 asort($allGroups);
                 ?>
<br>
<strong><?php echo TRANSLATION_USERGROUP;
                 ?></strong>
<br/>
<select name="group_id">
<option value="-" <?php if($row -> group_id === null){
                     echo "selected";
                     }
                 ?>>[Keine]</option>
<?php foreach($allGroups as $key => $value){
                     ?>
<option value="<?php echo $key;
                     ?>" <?php
                     if(intval($row -> group_id) == $key)
                        {
                         echo "selected";
                        
                         }
                     ?>><?php echo real_htmlspecialchars($value)?></option>
<?php }
                 ?>
</select>

<br/>

<!-- Legacy Rechtesystem -->
<input type="hidden" name="admin_rechte" value="<?php echo $row -> group;
                 ?>">


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


<strong><?php echo TRANSLATION_ICQ;
             ?></strong>   <br/>
<input type="text" name="icq_id" value="<?php echo $row -> icq_id?>">

<br/><br/>
<strong><?php echo TRANSLATION_SKYPE;
             ?></strong>   <br/>
<input type="text" name="skype_id" value="<?php echo $row -> skype_id?>">

<br/><br/>
<input type="checkbox" id="notify_on_login" name="notify_on_login" <?php
             if($row -> notify_on_login){
                 echo "checked='checked'";
                 }
             ?>><strong> <label for="notify_on_login"><?php echo TRANSLATION_NOTIFY_ON_LOGIN;
             ?></label></strong>
<br/>
<br/>

<strong><?php echo TRANSLATION_ABOUT_ME;
             ?></strong><br/>
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
