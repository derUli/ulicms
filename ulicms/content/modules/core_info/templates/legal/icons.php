<?php

use UliCMS\Security\PermissionChecker;

$currentAction = BackendHelper::getAction();
$icons = array(
    "legal_composer" => "fas fa-file-contract",
    "legal_npm" => "fas fa-file-contract",
);

$icons = array_filter($icons, function ($cssClass, $action) {
    $permissions = array(
        "legal_composer" => "info",
        "legal_npm" => "info"
    );

    $permissionChecker = new PermissionChecker(get_user_id());
    return $permissionChecker->hasPermission($permissions[$action]);
}, ARRAY_FILTER_USE_BOTH);

$selectedButton = "btn btn-primary";
$notSelectedButton = "btn btn-default"
?>
<div class="btn-toolbar" role="toolbar"
     aria-label="Toolbar with button groups">
    <div class="btn-group" role="group">
        <a href="<?php echo ModuleHelper::buildActionURL("info"); ?>"
           class="btn btn-default btn-back is-ajax"
           ><i class="fa fa-arrow-left"></i>
            <?php translate("back") ?></a>
    </div>
    <?php foreach ($icons as $action => $cssClass) { ?>
        <div class="btn-group" role="group">
            <a href="<?php echo ModuleHelper::buildActionURL($action); ?>"
               class="<?php 
               echo $action == $currentAction ?
                       $selectedButton : $notSelectedButton;
               ?> is-ajax">
                <i class="<?php echo $cssClass ?>"></i>
                <span class="hide-on-820">
                    <?php
                    (isset($specialLabels[$action]) ?
                                    esc(
                                            $specialLabels[$action]
                                    ) : translate(
                                            $action
                                    )
                            );
                    ?></span>
            </a>
        </div>
    <?php } ?>
</div>