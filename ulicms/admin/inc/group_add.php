<?php
if (! defined ( "ULICMS_ROOT" )) {
	die ( "Dummer Hacker!" );
}

$acl = new ACL ();
$all_permissions = $acl->getDefaultACL ( true, true );

?>
<form action="?action=groups" method="post">
<?php

csrf_token_html ();
?>
	<p>
		<strong><?php translate("name");?> </strong> <input type="text"
			required="required" name="name" value="">
	</p>
	<p>
		<strong><?php translate("permissions");?> </strong>
	</p>
	<fieldset>
		<p>
			<input id="checkall" type="checkbox" class="checkall"> <label
				for="checkall"><?php
				
				translate ( "select_all" );
				?> </label>
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
			?>"> <label for="<?php
			
			echo $key;
			?>"><?php
			
			echo $key;
			?> </label> <br />
<?php
		}
		?>
		</p>
	</fieldset>
	<br /> <input type="submit" value="<?php translate("create_group");?>"
		name="add_group">
</form>

<script type="text/javascript">
$(function () {
    $('.checkall').on('click', function () {
        $(this).closest('fieldset').find(':checkbox').prop('checked', this.checked);
    });
});
</script>

<?php
if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
	?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
}
?>