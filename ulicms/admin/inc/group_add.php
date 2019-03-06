<?php
$permissionChecker = new ACL();
$all_permissions = $permissionChecker->getDefaultACL(true, true);
$languages = Language::getAllLanguages();
?>
<form action="?action=groups" method="post">
<?php csrf_token_html ();?>
	<p>
		<strong><?php translate("name");?>*</strong> <input type="text"
			required="required" name="name" value="">
	</p>
	<h3><?php translate("permissions");?></h3>
	<fieldset>
		<p>
			<input id="checkall" type="checkbox" class="checkall"> <label
				for="checkall"><?php translate ( "select_all" );?> </label>
		</p>
		<p>
		<?php
foreach ($all_permissions as $key => $value) {
    ?>  <input type="checkbox" id="<?php esc($key);?>"
				name="user_permissons[]" value="<?php esc($key);?>"
				data-select-all-checkbox="#checkall"
				data-checkbox-group=".permission-checkbox"
				class="permission-checkbox"> <label
				for="<?php
    esc($key);
    ?>"><?php
    esc($key);
    ?> </label> <br />
<?php }?>
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
		<button name="add_group" type="submit" class="btn btn-primary">
			<i class="fa fa-save"></i> <?php translate("create_group");?></button>
	</p>
</form>
<script type="text/javascript">
$(function () {
    $('.checkall').on('click', function () {
        $(this).closest('fieldset').find(':checkbox').prop('checked', this.checked);
    });
});
</script>