<?php
if (! defined ( "ULICMS_ROOT" ))
die ( "Dummer Hacker!" );

$id = intval ( $_REQUEST ["edit"] );
$acl = new ACL ();
$all_permissions = $acl->getPermissionQueryResult ( $id );
$groupName = real_htmlspecialchars ( $all_permissions ["name"] );
$all_permissions_all = $acl->getDefaultACL ( false, true );
$all_permissions = json_decode ( $all_permissions ["permissions"], true );
foreach ( $all_permissions_all as $name => $value ) {
	if (! isset ( $all_permissions [$name] ))
	$all_permissions [$name] = $value;
}

ksort ( $all_permissions );

if ($all_permissions) {

	?>
<form action="?action=groups" method="post">
<?php

csrf_token_html ();
?>
	<input type="hidden" name="id" value="<?php
	
echo $id;
	?>">
	<p>
		<strong><?php

		echo TRANSLATION_NAME;
		?>
		</strong> <input type="text" required="true" name="name"
			value="<?php
	
echo $groupName;
	?>">
	</p>
	<p>
		<strong><?php

		echo TRANSLATION_PERMISSIONS;
		?>
		</strong>
	</p>
	<fieldset>
		<p>
			<input id="checkall" type="checkbox" class="checkall"> <label
				for="checkall"><?php

				echo TRANSLATION_SELECT_ALL;
				?>
			</label>
		</p>
		<p>
		<?php

		foreach ( $all_permissions as $key => $value ) {
			?>
			<input type="checkbox" id="<?php
		
echo $key;
		?>"
				name="user_permissons[]" value="<?php
		
echo $key;
		?>"
		<?php

		if ($value) {
			echo "checked='checked'";
		}
		?>> <label for="<?php
		
echo $key;
?>"><?php

echo $key;
?>
			</label><br />
			<?php

		}
		?>
		</p>
	</fieldset>
	<br /> <input type="submit"
		value="<?php
	
echo TRANSLATION_SAVE_CHANGES;
	?>" name="edit_group">
</form>

<script type="text/javascript">
$(function () {
    $('.checkall').on('click', function () {
        $(this).closest('fieldset').find(':checkbox').prop('checked', this.checked);
    });
});
</script>

	<?php
	if (getconfig ( "override_shortcuts" ) == "on" || getconfig ( "override_shortcuts" ) == "backend") {
		?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
		<?php

	}
	?>

	<?php

}

else {
	?>
<p style="color: red">Diese Gruppe ist nicht vorhanden.</p>
<?php
     }
