<?php
$acl = new ACL ();
if ($acl->hasPermission ( getModuleMeta ( "metadata_viewer", "metadata_viewer" ) ) and ViewBag::get ( "title" ) and ViewBag::get ( "content" )) {
	?><p>
	<a href="<?php echo ModuleHelper::buildAdminURL("metadata_viewer");?>"
		class="btn btn-default"><?php translate("back");?></a>
</p>
<h3><?php Template::escape(ViewBag::get("title"));?></h3>
<pre><?php Template::escape(ViewBag::get("content"));?></pre>
<?php
} else {
	noperms ();
}
