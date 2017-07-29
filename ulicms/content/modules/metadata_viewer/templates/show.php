<?php
$acl = new ACL ();
if ($acl->hasPermission ( getModuleMeta ( "metadata_viewer", "metadata_viewer" ) ) and ViewBag::get ( "title" ) and ViewBag::get ( "content" )) {
	?><p>
	<a href="<?php echo ModuleHelper::buildAdminURL("metadata_viewer");?>"
		class="btn btn-default"><?php translate("back");?></a>
</p>
<h3><?php Template::escape(ViewBag::get("title"));?></h3>

<a href=""></a>
<textarea cols="80" rows="20" readonly><?php Template::escape(ViewBag::get("content"));?></textarea>
<?php
} else {
	noperms ();
}
