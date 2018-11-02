<?php
use UliCMS\Security\PermissionChecker;

$permissionChecker = new PermissionChecker(get_user_id());
if ($permissionChecker->hasPermission("update_system")) {
    $updateInfo = checkForUpdates();
    if ($updateInfo) {
        echo $updateInfo;
    }
}