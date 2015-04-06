<h1><?php echo TRANSLATION_RESET_PASSWORD;?></h1>
<form action="index.php?reset_password" method="post">
<strong><?php translate("username");?></strong>
<br/>
<input type="text" name="username" value="">
<br/><br/>
<input type="submit" value="<?php translate("reset_password");?>">
</form>