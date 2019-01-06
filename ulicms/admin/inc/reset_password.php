<?php
if (Settings::get("disable_password_reset")) {
    translate("function_is_disabled");
} else {
    ?>
<?php
    $messame = null;
    if (isset($_POST["username"]) and ! empty($_POST["username"])) {
        $username = $_POST["username"];
        $user = getUserByName($username);
        if ($user) {
            $passwordReset = new PasswordReset();
            $token = $passwordReset->addToken($user["id"]);
            $passwordReset->sendMail($token, $user["email"], get_ip(), $user["firstname"], $user["lastname"]);
            $message = get_translation("PASSWORD_RESET_SUCCESSFULL");
        } else {
            $message = get_translation("NO_SUCH_USER");
        }
    }
    ?>
<p>
	<a href="./" class="btn btn-default btn-back"><i
		class="fa fa-arrow-left"></i> <?php
    
    translate("back_to_login");
    ?></a>
</p>
<h1>
<?php translate("reset_password");?>
</h1>
<form action="index.php?reset_password" method="post">
<?php
    csrf_token_html();
    ?><p>
		<strong><?php
    
    translate("username");
    ?>
	</strong> <br /> <input type="text" name="username" value="" required>
	</p>
	<p>
		<button type="submit" class="btn btn-warning"><i class="fa fa-lock"></i> <?php
    translate("reset_password");
    ?></button>
	</p>
		<?php
    if ($message) {
        ?>
	<p class="ulicms_error">
	<?php
        echo htmlspecialchars($message);
        ?>
	</p>

	<?php
    }
    ?>
</form>
<?php
} 
