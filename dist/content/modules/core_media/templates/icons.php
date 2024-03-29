<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;

$currentAction = \App\Helpers\BackendHelper::getAction();
$icons = [
    'files' => 'fa fa-file',
    'videos' => 'fa fa-file-video',
    'audio' => 'fa fa-file-audio'
];
$icons = array_filter($icons, static function($cssClass, $action) {
    $permissionChecker = new PermissionChecker(get_user_id());
    return $permissionChecker->hasPermission($action);
}, ARRAY_FILTER_USE_BOTH);
$selectedButton = 'btn btn-primary';
$notSelectedButton = 'btn btn-light';
?>
<div class="btn-toolbar" role="toolbar"
     aria-label="Toolbar with button groups">
    <div class="btn-group" role="group">
        <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('media'); ?>"
           class="btn btn-light btn-back is-ajax"><i
                class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
    </div>
    <?php foreach ($icons as $action => $cssClass) { ?>
        <div class="btn-group" role="group">
            <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL($action); ?>"
               class="<?php
               echo $action == $currentAction ?
                       $selectedButton : $notSelectedButton;
        echo $action == 'files' ? ' is-ajax' : ' is-not-ajax';
        ?>">
                <i class="<?php echo $cssClass; ?>"></i>
                <span class="hide-on-820">
                    <?php translate($action); ?>
                </span>
            </a>
        </div>
    <?php } ?>
</div>