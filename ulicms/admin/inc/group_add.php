<?php
$acl = new ACL ();
$all_permissions = $acl->getDefaultACL ( true, true );
$languages = Language::getAllLanguages ();
?>
<form action="?action=groups" method="post">
<?php

csrf_token_html ();
?>
	<p>
		<strong><?php translate("name");?> </strong> <input type="text"
			required="required" name="name" value="">
	</p>
	<h3><?php translate("permissions");?></h3>

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
	<h4><?php translate("languages");?></h4>
	<fieldset>
		<p>
		<?php foreach($languages as $lang){?>
			
			<input type="checkbox" name="restrict_edit_access_language[]"
				value="<?php echo $lang->getID();?>"
				id="lang-<?php echo $lang->getID();?>"> <label
				for="lang-<?php echo $lang->getID();?>"><?php Template::escape($lang->getName());?></label>
			<br />
			 
		<?php }?></p>
	</fieldset>
	<h4><?php translate("allowable_tags");?></h4>
	<input type="text" name="allowable_tags"
		value="<?php Template::escape(HTML5_ALLOWED_TAGS);?>"><br /> <small><?php translate("allowable_tags_help");?></small>
	<br /> <br />
	<p>
		<input type="submit" value="<?php translate("create_group");?>"
			name="add_group">
	</p>

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