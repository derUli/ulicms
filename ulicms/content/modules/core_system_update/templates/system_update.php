<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("update_system")) {
    if (is_file(Path::resolve("ULICMS_ROOT/update.php"))) {
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
