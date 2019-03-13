<?php
if (Settings::get("visitors_can_register") == "off" or ! Settings::get("visitors_can_register")) {
    translate("FUNCTION_IS_DISABLED");
} else {
    
    $language = getSystemLanguage();
    $checkbox = new PrivacyCheckbox($language);
    
    $errors = false;
    if (isset($_POST["register_user"])) {
        if ($checkbox->isEnabled() and ! $checkbox->isChecked()) {
            echo "<p style='color:red;'>" . get_translation("please_accept_privacy_conditions") . "</p>";
        } else if (empty($_POST["username"]) or empty($_POST["password"]) or empty($_POST["firstname"]) or empty($_POST["lastname"])) {
            echo "<p style='color:red;'>" . get_translation("FILL_ALL_FIELDS") . "</p>";
        } else if (user_exists($_POST["username"])) {
            echo "<p style='color:red;'>" . get_translation("USERNAME_ALREADY_EXISTS") . "</p>";
        } else if ($_POST["password"] != $_POST["password_repeat"]) {
            echo "<p style='color:red;'>" . get_translation("PASSWORD_REPEAT_IS_WRONG") . "</p>";
        } else {
            do_event("before_user_registration");
            adduser($_POST["username"], $_POST["lastname"], $_POST["firstname"], $_POST["email"], $_POST["password"], false);
            do_event("after_user_registration");
            
            echo "<p style='color:green;'>" . get_translation("REGISTRATION_SUCCESSFUL") . "</p>";
            if (! empty($_REQUEST["go"])) {
                $go = htmlspecialchars($_REQUEST["go"]);
            } else {
                $go = "index.php";
            }
            echo "<p><a href='$go'>" . get_translation("continue_here") . "</a></p>";
        }
    }
    ?>
<?php
    
    do_event("before_register_form_title");
    ?>
<p>
	<a href="./" class="btn btn-default btn-back"><i
		class="fa fa-arrow-left"></i> <?php
    
    translate("back_to_login");
    ?></a>
</p>
<h1>
<?php translate("registration");?>
</h1>

<?php
    
    do_event("before_register_form");
    ?>
<form action="index.php?register=register" method="post">
<?php
    
    csrf_token_html();
    ?>
	<input type="hidden" name="register_user" value="add_admin">
	<?php
    
    if (! empty($_REQUEST["go"])) {
        ?>
	<input type="hidden" name="go"
		value='<?php
        echo htmlspecialchars($_REQUEST["go"])?>'>
	<?php
    }
    ?>
	<strong><?php translate("username");?>
	</strong><br /> <input type="text" required="required"
		name="username" value=""> <br /> <strong><?php translate("lastname")?>
	</strong><br /> <input type="text" required="required"
		name="lastname" value=""> <br /> <strong><?php translate("firstname");?>
	</strong><br /> <input type="text" required="required"
		name="firstname" value=""><br /> <strong><?php translate("email");?>
	</strong><br /> <input type="email" required="required"
		name="email" value=""><br /> <strong><?php translate("password");?>
	</strong><br /> <input type="password" required="required"
		name="password" value=""><br /> <strong><?php translate("password_repeat");?>
	</strong><br /> <input type="password" required="required"
		name="password_repeat" value="">
		<?php do_event ( "register_form_field" );?>
		<div class="privacy-checkbox">
		<?php
    echo $checkbox->render();
    ?></div>
	<p class="voffset3">
		<button type="submit" class="btn btn-primary">
			<i class="fas fa-user-plus"></i> <?php translate("register");?></button>
	</p>
</form>
<?php
    do_event("after_register_form");
}