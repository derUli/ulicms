<?php 
if(!defined("MODULE_ADMIN_REQUIRED_PERMISSION"))
  die("Lam0r! ^^");
?>
<form action="<?php echo getModuleAdminSelfPath()?>" method="post">
<p><textarea name="sql_code" id="sql_code"></textarea></p>
<input type="submit" value="Ausführen">
</form>