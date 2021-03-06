<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("install_packages")) {
    if (isset($_REQUEST["file"])) {
        $file = Template::getEscape($_REQUEST["file"]); ?>
        <h1><?php translate("install_package"); ?></h1>
        <p><?php translate("PACKAGE_SUCCESSFULLY_UPLOADED", array("%file%" => $file)); ?></p>
        <p>
            <a href="<?php echo ModuleHelper::buildActionURL("upload_package"); ?>"
               class="btn btn-warning"><i class="fas fa-box"></i> <?php translate("install_another_package") ?></a>
        </p>
        <?php
    }
} else {
    noPerms();
}
