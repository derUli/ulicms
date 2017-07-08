<?php
$acl = new ACL ();
if ($acl->hasPermission ( "version_info" )) {
	?>
	| UliCMS <?php Template::escape(cms_version());?> | PHP  <?php Template::escape(phpversion());?> |
	 <?php Template::escape($_SERVER['SERVER_SOFTWARE']);?> | MySQL <?php Template::escape(Database::getServerVersion());?>
</small>
<?php
}
