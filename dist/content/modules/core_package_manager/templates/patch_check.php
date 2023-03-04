<?php

use App\Packages\PatchManager;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("update_system") && !Settings::get("disable_core_patch_check")) {
    $patchManager = new PatchManager();
    $data = $patchManager->fetchPackageIndex();
    $data = trim($data);
    if (!empty($data)) {
        ?>
        <div class="alert alert-info"><?php translate("patches_will_fix_errors"); ?></div>
        <a href="?action=available_patches" class="btn btn-primary">
            <i class="fas fa-eye"></i> <?php translate("show_available_patches"); ?></a>
        <?php
    }
}
