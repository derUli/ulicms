<?php
if (getconfig ("disable_password_reset"))
     die (TRANSLATION_FUNCTION_IS_DISABLED);
?>

<?php
$messame = null;
if (isset ($_POST ["username"]) and ! empty ($_POST ["username"])){
     $username = db_escape ($_POST ["username"]);
     if (resetPassword ($username))
         $message = TRANSLATION_PASSWORD_RESET_SUCCESSFULL;
     else
         $message = TRANSLATION_NO_SUCH_USER;
    }
?>
<h1>
<?php

echo TRANSLATION_RESET_PASSWORD;
?>
</h1>
<p>
	<a href="./">[<?php translate("back_to_login");
?>]</a>
</p>
<form action="index.php?reset_password" method="post">
	<strong><?php

 translate ("username");
 ?>
	</strong> <br /> <input type="text" name="username" value=""> <br /> <br />
	<input type="submit"
		value="<?php

 translate ("reset_password");
 ?>">
		<?php

 if ($message){
     ?>
	<p class="ulicms_error">
	<?php
    
     echo htmlspecialchars ($message);
     ?>
	</p>

	<?php
     }
 ?>
</form>
