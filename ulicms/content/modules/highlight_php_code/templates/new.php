<?php
$acl = new ACL ();
if ($acl->hasPermission ( getModuleMeta ( "highlight_php_code", "admin_permission" ) )) {
	?>
<form
	action="<?php Template::escape(ModuleHelper::buildMethodCall("HighlightPHPCode", "create"));?>"
	method="post">
<?php csrf_token_html()?>
<p>
		<strong><?php translate("name")?></strong><br /> <input type="text"
			name="name" maxlength="140" value="">
	</p>
	<!--  @FIXME: CodeMirror verwenden -->
	<p>
		<strong><?php translate("code")?></strong><br />
		<textarea cols="80" rows="8"></textarea>
	</p>
	<button type="submit" class="btn btn-success"><?php translate("save");?></button>
</form>
<?php }?>