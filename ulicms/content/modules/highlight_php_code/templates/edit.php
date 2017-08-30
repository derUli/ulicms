<?php
$acl = new ACL ();
if ($acl->hasPermission ( getModuleMeta ( "highlight_php_code", "admin_permission" ) ) and Request::hasVar ( "id" )) {
	$id = Request::getVar ( "id", null, int );
	if ($id) {
		$data = new PHPCode ( $id );
		if ($data->getID ()) {
			?>
<?php echo ModuleHelper::buildMethodCallForm("HighlightPHPCode", "editCode", array("id" => $data->getID()));?>
<p>
	<strong><?php translate("name")?></strong><br /> <input type="text"
		name="name" maxlength="140"
		value="<?php Template::escape($data->getName());?>" required>
</p>
<!--  @FIXME: CodeMirror verwenden -->
<p>
	<strong><?php translate("code")?></strong><br />
	<textarea cols="80" rows="8" name="code" required><?php Template::escape($data->getCode());?></textarea>
</p>
<button type="submit" class="btn btn-success"><?php translate("save");?></button>
</form>
<?php
		}
	}
} else {
	noperms ();
}
?>
