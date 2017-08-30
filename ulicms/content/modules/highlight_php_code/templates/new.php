<?php
$acl = new ACL ();
if ($acl->hasPermission ( getModuleMeta ( "highlight_php_code", "admin_permission" ) )) {
	?>
<?php echo ModuleHelper::buildMethodCallForm("HighlightPHPCode", "createCode");?>
<p>
	<strong><?php translate("name")?></strong><br /> <input type="text"
		name="name" maxlength="140" value="" required>
</p>
<!--  @FIXME: CodeMirror verwenden -->
<p>
	<strong><?php translate("code")?></strong><br />
	<textarea cols="80" rows="8" name="code" required></textarea>
</p>
<button type="submit" class="btn btn-success"><?php translate("save");?></button>
</form>
<?php

} else {
	noperms ();
}
?>