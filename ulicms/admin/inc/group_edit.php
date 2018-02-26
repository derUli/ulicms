<?php
$acl = new ACL ();

if (! $acl->hasPermission ( "groups" )) {
	noperms ();
} else {
	$id = intval ( $_REQUEST ["edit"] );
	$acl = new ACL ();
	$all_permissions = $acl->getPermissionQueryResult ( $id );
	$groupName = real_htmlspecialchars ( $all_permissions ["name"] );
	$all_permissions_all = $acl->getDefaultACL ( false, true );
	$all_permissions = json_decode ( $all_permissions ["permissions"], true );
	foreach ( $all_permissions_all as $name => $value ) {
		if (! isset ( $all_permissions [$name] )) {
			$all_permissions [$name] = $value;
		}
	}
	
	$languages = Language::getAllLanguages ();
	$group = new Group ( $id );
	$selectedLanguages = $group->getLanguages ();
	
	ksort ( $all_permissions );
	
	if ($all_permissions) {
		?>
<form action="?action=groups" method="post">
<?php csrf_token_html ();?>
	<input type="hidden" name="id" value="<?php	echo $id;?>">
	<p>
		<strong><?php translate("name");?></strong> <input type="text"
			required="required" name="name" value="<?php echo $groupName;?>">
	</p>
	<h3><?php translate("permissions");?></h3>
	<fieldset>
		<p>
			<input id="checkall" type="checkbox" class="checkall"> <label
				for="checkall"><?php translate("select_all");?>
			</label>
		</p>
		<p>
		<?php
		foreach ( $all_permissions as $key => $value ) {
			?>
			<input type="checkbox" id="<?php echo $key;?>"
				name="user_permissons[]" value="<?php echo $key;?>"
				<?php
			
			if ($value) {
				echo "checked='checked'";
			}
			?>> <label for="<?php echo $key;?>"><?php echo $key;?>
			</label><br />
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
				<?php if(in_array($lang, $selectedLanguages)){ echo "checked";}?>
				id="lang-<?php echo $lang->getID();?>"> <label
				for="lang-<?php echo $lang->getID();?>"><?php Template::escape($lang->getName());?></label>
			<br />
		<?php }?></p>
	</fieldset>
	<h4><?php translate("allowable_tags");?></h4>
	<input type="text" name="allowable_tags"
		value="<?php Template::escape($group->getAllowableTags());?>"><br /> <small><?php translate("allowable_tags_help");?></small>
	<br /> <br />
	<button name="edit_group" type="submit" class="btn btn-primary"><?php translate("save_changes");?></button>
</form>
<script type="text/javascript">
$(function () {
    $('.checkall').on('click', function () {
        $(this).closest('fieldset').find(':checkbox').prop('checked', this.checked);
    });
});
</script>
<?php
	} else {
		?>
<p style="color: red">Diese Gruppe ist nicht vorhanden.</p>
<?php
	}
}
