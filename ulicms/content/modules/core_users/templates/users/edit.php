<?php
$acl = new ACL ();
if (($acl->hasPermission ( "users" ) and $acl->hasPermission ( "users_edit" )) or ($_GET ["admin"] == $_SESSION ["login_id"])) {
	$admin = intval ( $_GET ["admin"] );
	$languages = getAvailableBackendLanguages ();
	$query = db_query ( "SELECT * FROM " . tbname ( "users" ) . " WHERE id='$admin'" );
	while ( $row = db_fetch_object ( $query ) ) {
		?>
<form action="index.php?sClass=UserController&sMethod=update"
	name="userdata_form" method="post" enctype="multipart/form-data"
	id="edit_user" autocomplete="off">
	<?php csrf_token_html ();?>
	<img src="<?php
		echo get_gravatar ( $row->email, 200 );
		?>"
		alt="Avatar Image" /> <br /> <br /> <input type="hidden"
		name="edit_admin" value="edit_admin"> <input type="hidden" name="id"
		value="<?php
		echo $row->id;
		?>"> <strong><?php translate("username");?></strong><br /> <input
		type="text" name="admin_username"
		value="<?php echo real_htmlspecialchars($row->username);?>"
		<?php
		if (! $acl->hasPermission ( "users" )) {
			?>
		readonly="readonly" <?php
		}
		?>> <br /> <br /> <strong><?php translate("lastname");?></strong><br />
	<input type="text" name="admin_lastname"
		value="<?php
		
		echo real_htmlspecialchars ( $row->lastname );
		?>"> <br /> <br /> <strong><?php translate("firstname");?></strong><br />
	<input type="text" name="admin_firstname"
		value="<?php
		
		echo real_htmlspecialchars ( $row->firstname );
		?>"
		required="required"><br /> <br /> <strong><?php translate("email");?></strong><br />
	<input type="email" name="admin_email"
		value="<?php
		
		echo real_htmlspecialchars ( $row->email );
		?>"><br /> <br /> <strong><?php translate("last_login");?></strong><br />
			<?php
		if (is_null ( $row->last_login )) {
			translate ( "never" );
		} else {
			echo strftime ( "%x %X", $row->last_login );
		}
		
		?><br /> <br /> <strong><?php translate("new_password");?></strong><br />
	<input type="password" name="admin_password" id="admin_password"
		value="" autocomplete="off"><br /> <br /> <strong><?php translate("password_repeat");?></strong><br />
	<input type="password" name="admin_password_repeat"
		id="admin_password_repeat" value="" autocomplete="off"> <br />
	<?php
		$acl = new ACL ();
		if ($acl->hasPermission ( "users" )) {
			$allGroups = $acl->getAllGroups ();
			asort ( $allGroups );
			?>
	<br> <strong><?php translate("usergroup");?></strong> <br /> <select
		name="group_id">
		<option value="-"
			<?php
			
			if ($row->group_id === null) {
				echo "selected";
			}
			?>>[<?php translate("none");?>]</option>
		<?php
			
			foreach ( $allGroups as $key => $value ) {
				?>
		<option value="<?php
				
				echo $key;
				?>"
			<?php
				if (intval ( $row->group_id ) == $key) {
					echo "selected";
				}
				?>>
					<?php echo real_htmlspecialchars($value)?>
		</option>
		<?php
			}
			?>
	</select> <br />
	<!-- Legacy Rechtesystem -->
	<input type="hidden" name="admin_rechte"
		value="<?php
			
			echo $row->group;
			?>">
				<?php
		} else {
			?>
	<input type="hidden" name="admin_rechte"
		value="<?php echo $row -> group?>"> <input type="hidden"
		name="group_id"
		value="<?php
			if (! $_SESSION ["group_id"]) {
				echo "-";
			} else {
				echo $_SESSION ["group_id"];
			}
			?>">
		<?php
		}
		?>
	<br /> <strong><?php
		
		translate ( "homepage" );
		?></strong> <br /> <input type="url" name="homepage"
		value="<?php echo real_htmlspecialchars($row -> homepage);?>"> <br />
	<br /> <strong><?php
		
		translate ( "twitter_profile" );
		?></strong> <br /> <input type="text" name="twitter"
		value="<?php echo real_htmlspecialchars($row -> twitter);?>"> <br /> <br />
	<strong><?php translate("skype");?></strong> <br /> <input type="text"
		name="skype_id"
		value="<?php echo real_htmlspecialchars($row -> skype_id);?>"> <br />
	<br /> <strong><?php translate("html_editor");?></strong> <br /> <select
		name="html_editor">
		<option value="ckeditor"
			<?php if(!$row -> html_editor or $row -> html_editor == "ckeditor") echo "selected"?>>CKEditor</option>
		<option value="codemirror"
			<?php if($row -> html_editor == "codemirror") echo "selected"?>>CodeMirror</option>
	</select> <br /> <br /> <input type="checkbox" id="notify_on_login"
		name="notify_on_login"
		<?php
		if ($row->notify_on_login) {
			echo "checked='checked'";
		}
		?>><strong> <label for="notify_on_login"><?php translate("NOTIFY_ON_LOGIN");?></label>
	</strong> <br /> <br /> <input type="checkbox" value="1"
		<?php
		
		if ($row->require_password_change) {
			echo "checked";
		}
		?>
		name="require_password_change" id="require_password_change"> <label
		for="require_password_change"><?php
		
		translate ( "REQUIRE_PASSWORD_CHANGE_ON_NEXT_LOGIN" );
		?> </label>
		<?php
		
		if ($acl->hasPermission ( "users" )) {
			?>
	<br /> <br /> <input type="checkbox" value="1" name="admin" id="admin"
		<?php
			
			if ($row->admin) {
				echo "checked";
			}
			?>> <label for="admin"><?php
			
			translate ( "is_admin" );
			?> </label> <span style="cursor: help;"
		onclick="$('div#is_admin').slideToggle()">[?]</span>
	<div id="is_admin" class="help" style="display: none">
	<?php
			echo nl2br ( get_translation ( "HELP_IS_ADMIN" ) );
			?>
	</div>

	<br /> <br /> <input type="checkbox" value="1" name="locked"
		id="locked"
		<?php
			
			if ($row->locked) {
				echo "checked";
			}
			?>> <label for="locked"><?php
			
			translate ( "locked" );
			?> </label>
	<?php
		} else {
			echo '<input type="hidden" name="admin" value="' . $row->admin . '">';
			if ($row->locked) {
				echo '<input type="hidden" name="locked" value="' . $row->locked . '">';
			}
		}
		?>
			<br /> <br /> <strong><?php translate("default_language");?></strong><br />
	<select name="default_language">
		<option value="" <?php if(!$row->default_language) echo " selected";?>>[<?php translate("standard");?>]</option>
		<?php
		for($i = 0; $i < count ( $languages ); $i ++) {
			if ($row->default_language == $languages [$i]) {
				echo '<option value="' . $languages [$i] . '" selected>' . getLanguageNameByCode ( $languages [$i] ) . '</option>';
			} else {
				echo '<option value="' . $languages [$i] . '">' . getLanguageNameByCode ( $languages [$i] ) . '</option>';
			}
		}
		?>
	</select> <br /> <br /> <strong><?php translate("about_me");?></strong><br />
	<textarea rows=10 cols=50 name="about_me"><?php echo htmlspecialchars($row->about_me)?></textarea>
	<br /> <br />
	<button type="submit" class="btn btn-success"><?php translate ( "OK" );?></button>
</form>

<?php
		break;
	}
	?>
		<?php
} else {
	noperms ();
}
