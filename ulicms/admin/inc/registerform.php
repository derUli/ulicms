<?php
if(getconfig("visitors_can_register") == "off" or !getconfig("visitors_can_register"))
     die(TRANSLATION_FUNCTION_IS_DISABLED);

$errors = false;
if(isset($_POST["register_user"])){
    
     if(empty($_POST["admin_username"]) or empty($_POST["admin_password"]) or empty($_POST["admin_firstname"]) or empty($_POST["admin_lastname"])){
         echo "<p style='color:red;'>" . TRANSLATION_FILL_ALL_FIELDS . "</p>";
        
         }
    else if(user_exists($_POST["admin_username"])){
         echo "<p style='color:red;'>" . TRANSLATION_USERNAME_ALREADY_EXISTS . "</p>";
         }
    else if($_POST["admin_password"] != $_POST["admin_password_repeat"]){
         echo "<p style='color:red;'>" . TRANSLATION_PASSWORD_REPEAT_IS_WRONG . "</p>";
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
             $registered_user_default_level, false);
         echo "<p style='color:green;'>Registrierung erfolgreich!</p>";
         if(!empty($_REQUEST["go"])){
             $go = htmlspecialchars($_REQUEST["go"]);
             }else{
             $go = "index.php";
             }
         echo "<p><a href='$go'>" . TRANSLATION_CONTINUE_HERE . "</a></p>";
         }
     }

?>
<?php add_hook("before_register_form_title");
?>
<h1><?php echo TRANSLATION_REGISTRATION;
?></h1>
<?php add_hook("before_register_form");
?>
<form action="index.php?register=register" method="post">
<?php csrf_token_html();
?>
<input type="hidden" name="register_user" value="add_admin">
<?php if(!empty($_REQUEST["go"])){
     ?>
<input type="hidden" name="go" value='<?php
     echo htmlspecialchars($_REQUEST["go"])?>'>
<?php }
?>
<strong><?php echo TRANSLATION_USERNAME;
?></strong><br/>
<input type="text" required="true" name="admin_username" value="">
<br/><br/>
<strong><?php echo TRANSLATION_LASTNAME;
?></strong><br/>
<input type="text" required="true" name="admin_lastname" value="">
<br/><br/>
<strong><?php echo TRANSLATION_FIRSTNAME;
?></strong><br/>
<input type="text" required="true" name="admin_firstname" value=""><br/><br/>
<strong><?php echo TRANSLATION_EMAIL;
?></strong><br/>
<input type="email" required="true" name="admin_email" value=""><br/><br/>
<strong><?php echo TRANSLATION_PASSWORD;
?></strong><br/>
<input type="password" required="true" name="admin_password" value=""><br/><br/>

<strong><?php echo TRANSLATION_PASSWORD_REPEAT;
?></strong></strong><br/>
<input type="password" required="true"name="admin_password_repeat" value=""><br/><br/>
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
<input type="submit" value="<?php echo TRANSLATION_REGISTER;
?>">
</form>

<?php add_hook("after_register_form");
?>
