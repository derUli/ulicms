<?php
$acl = new ACL();
if ($acl->hasPermission("update_system")) {
    if (is_file(Path::resolve("ULICMS_ROOT/update.php"))) {
        ?>
<p>
	<a href="../update.php" class="btn btn-warning"><?php translate("run_update");?></a>
</p>
<p><?php translate("update_notice");?>
	<?php
    } else {
        translate("update_information_text");
        ?>
</p>
<?php
    }
} else {
    noPerms();
}
