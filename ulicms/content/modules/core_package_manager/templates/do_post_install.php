<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("update_system")) {
    ?>
    <h1><?php translate("run_post_install_script"); ?></h1>
    <?php
    $postinstall = ULICMS_DATA_STORAGE_ROOT . "/post-install.php";
    if (file_exists($postinstall)) {
        require $postinstall;
        unlink($postinstall);
        ?>
        <?php if (!file_exists($postinstall)) { ?>
            <p><?php translate("finished"); ?></p>
        <?php } ?>
        <?php
    } else {
        ?>
        <p><?php translate("file_not_found"); ?></p>
        <?php
    }
} else {
    noPerms();
}
