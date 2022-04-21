<?php
if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Security\Permissions\PermissionChecker;

$permissionChecker = new PermissionChecker(get_user_id());

if ($permissionChecker->hasPermission("update_system")) {
    if (file_exists(Path::resolve(ULICMS_UPDATE_FILE))) {
        ?>

        <div class="alert alert-danger">
        <?php translate("update_notice"); ?>
        </div>
        <div>
            <a href="../update.php" class="btn btn-warning">
                <i class="fas fa-sync"></i>
        <?php translate("run_update"); ?></a>
        </div>
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
