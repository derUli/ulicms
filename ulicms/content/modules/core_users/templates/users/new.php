<?php
$acl = new ACL();
if ($acl->hasPermission("users") and $acl->hasPermission("users_create")) {
    $languages = getAvailableBackendLanguages();
    $default_language = getSystemLanguage();
    $ref = _esc(Request::getVar("ref", "home"));
    ?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("admins");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<form action="index.php?sClass=UserController&sMethod=create"
	method="post" autocomplete="off" id="edit_user">
<?php csrf_token_html();?>
	<input type="hidden" name="add_admin" value="add_admin"> <strong><?php translate("username");?>*</strong><br />
	<input type="text" required="required" name="admin_username" value="">
	<br /> <strong><?php translate("lastname");?></strong><br /> <input
		type="text" name="admin_lastname" value=""> <br /> <strong><?php translate("firstname");?></strong><br />
	<input type="text" name="admin_firstname" value=""><br /> <strong><?php translate("email");?></strong><br />
	<input type="email" name="admin_email" value=""><br /> <strong><?php translate("password");?>*</strong><br />
	<input type="password" required="required" name="admin_password"
		id="admin_password" value="" autocomplete="off"> <br /> <strong><?php translate("password_repeat");?>*</strong><br />
	<input type="password" required="required" name="admin_password_repeat"
		id="admin_password_repeat" value="" autocomplete="off">
		<?php
    $acl = new ACL();
    $allGroups = $acl->getAllGroups();
    asort($allGroups);
    ?>
	<br /> <strong><?php translate("primary_group");?></strong> <br /> <select
		name="group_id">
		<option value="-"
			<?php
    
    if ($row->group_id === null) {
        echo "selected";
    }
    ?>>[<?php translate("none");?>]</option>
		<?php
    
    foreach ($allGroups as $key => $value) {
        ?>
		<option value="<?php
        
        echo $key;
        ?>"
			<?php
        if (Settings::get("default_acl_group") == $key) {
            echo "selected";
        }
        ?>>
					<?php echo real_htmlspecialchars($value)?>
		</option>
		<?php
    }
    ?>
	</select> <br /> <br /> <input type="checkbox" value="1"
		name="require_password_change" id="require_password_change"> <label
		for="require_password_change"><?php translate ( "REQUIRE_PASSWORD_CHANGE_ON_NEXT_LOGIN" );?> </label>
	<br /> <br /> <input type="checkbox" id="send_mail" name="send_mail"
		value="sendmail"> <label for="send_mail"><?php translate("SEND_LOGINDATA_BY_MAIL");?></label>
	<br /> <br /> <input type="checkbox" value="1" name="admin" id="admin">
	<label for="admin"><?php translate ( "is_admin" );?> </label><span
		style="cursor: help;" onclick="$('div#is_admin').slideToggle()">[?]</span><br />
	<div id="is_admin" class="help" style="display: none">
	<?php
    echo nl2br(get_translation("HELP_IS_ADMIN"));
    ?>
	</div>

	<br /> <input type="checkbox" value="1" name="locked" id="locked"> <label
		for="locked"><?php translate ( "locked" );?> </label> <br /> <br /> <strong><?php translate("default_language");?></strong><br />
	<select name="default_language">
		<option value="" selected>[<?php translate("standard");?>]</option>
		<?php
    for ($i = 0; $i < count($languages); $i ++) {
        echo '<option value="' . $languages[$i] . '">' . getLanguageNameByCode($languages[$i]) . '</option>';
    }
    ?>
	</select><br /> <br />
	<button type="submit" class="btn btn-primary"><?php translate ( "create_user" );?></button>
</form>
<?php
} else {
    noPerms();
}
