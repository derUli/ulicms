<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

$permissionChecker = new \App\Security\Permissions\ACL();
if ($permissionChecker->hasPermission('update_system')) {
    ?>
    <h1><?php translate('run_post_install_script'); ?></h1>
    <?php
    $postinstall = ULICMS_ROOT . '/post-install.php';
    if (is_file($postinstall)) {
        require $postinstall;
        unlink($postinstall);
        ?>
        <?php if (! is_file($postinstall)) { ?>
            <p><?php translate('finished'); ?></p>
        <?php } ?>
        <?php
    } else {
        ?>
        <p><?php translate('file_not_found'); ?></p>
        <?php
    }
} else {
    noPerms();
}
