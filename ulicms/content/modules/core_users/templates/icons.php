<?php

use UliCMS\Security\PermissionChecker;

$currentAction = BackendHelper::getAction();
$icons = array(
    "admins" => "fa fa-user",
    "groups" => "fa fa-users"
);

$icons = array_filter($icons, function ($cssClass, $action) {
    $permissions = array(
        "admins" => "users",
        "groups" => "groups"
    );

    $permissionChecker = new PermissionChecker(get_user_id());
    return $permissionChecker->hasPermission($permissions[$action]);
}, ARRAY_FILTER_USE_BOTH);

$specialLabels = array(
    "admins" => get_translation("users")
);

$selectedButton = "btn btn-primary";
$notSelectedButton = "btn btn-default"
?>

<div class="btn-toolbar" role="toolbar"
     aria-label="Toolbar with button groups">

    <?php foreach ($icons as $action => $cssClass) { ?>
        <div class="btn-group" role="group">
            <a href="<?php echo ModuleHelper::buildActionURL($action); ?>"
               class="<?php echo $action == $currentAction ? $selectedButton : $notSelectedButton; ?>">
                <i class="<?php echo $cssClass ?>"></i> <?php (isset($specialLabels[$action]) ? esc($specialLabels[$action]) : translate($action)); ?>
            </a>
        </div>

    <?php } ?>
</div>