<?php
$acl = new ACL ();
$permission = ( array ) getModuleMeta ( "expire_users", "action_permissions" );
$permission = $permission ["edit_expire_user"];
$user = new User ( Request::getVar ( "id", 0, "int" ) );
if ($acl->hasPermission ( $permission ) and $user->getId ()) {
	$expire_date = UserSettings::get ( "expire_date", "int", $user->getId () ) ? UserSettings::get ( "expire_date", "int", $user->getId () ) : null;
	$expire_date = ! is_null ( $expire_date ) ? ExpireUsers::formatDate ( $expire_date ) : "";
	?>
<?php echo ModuleHelper::buildMethodCallForm("ExpireUsers", "save", array("id"=>Request::getVar("id", 0, "int")));?>
<p>
	<strong><?php translate("username");?></strong><br />
	<?php Template::escape($user->getUsername());?>
</p>
<p>
	<strong><?php translate("lastname");?></strong><br />
	<?php Template::escape($user->getLastname());?>
</p>
<p>
	<strong><?php translate("firstname");?></strong><br />
	<?php Template::escape($user->getFirstname());?>
</p>
<p>
	<input type="checkbox" name="locked" id="locked" value="1"
		<?php if($user->getLocked()) echo "checked";?>> <label for="locked"><?php translate("locked")?></label>
</p>

<p>
	<label for="expire_date"><?php translate("expire_date")?></label><br />
	<input type="text" name="expire_date" id="expire_date"
		value="<?php Template::escape($expire_date);?>"> <br /> <small><?php translate("expected_date_format", array("%format%"=> ExpireUsers::formatDate(time())));?></small>
</p>

<div class="alert alert-info"><?php translate("expire_date_help");?></div>
<button type="submit" class="btn btn-success"><?php translate("save");?></button>
</form>
<?php }?>
