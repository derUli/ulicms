<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;

$currentAction = \App\Helpers\BackendHelper::getAction();
$icons = [
    'pages' => 'fas fa-book',
    'comments_manage' => 'fa fa-comment',
    'forms' => 'fab fa-wpforms',
    'categories' => 'fa fa-list-alt'
];

$icons = array_filter($icons, static function($cssClass, $action) {
    $permissions = [
        'pages' => 'pages',
        'comments_manage' => 'comments_manage',
        'forms' => 'forms',
        'categories' => 'categories'
    ];

    $permissionChecker = new PermissionChecker(get_user_id());
    return $permissionChecker->hasPermission($permissions[$action]);
}, ARRAY_FILTER_USE_BOTH);

$specialLabels = [
    'comments_manage' => get_translation('comments')
];

$selectedButton = 'btn btn-primary';
$notSelectedButton = 'btn btn-light';
?>

<div class="btn-toolbar" role="toolbar"
     aria-label="Toolbar with button groups">
    <div class="btn-group" role="group">
        <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('contents'); ?>"
           class="btn btn-light btn-back is-ajax"
           ><i class="fa fa-arrow-left"></i>
            <?php translate('back'); ?></a>
    </div>
    <?php foreach ($icons as $action => $cssClass) { ?>
        <div class="btn-group" role="group">
            <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL($action); ?>"
               class="<?php
               echo $action == $currentAction ?
                       $selectedButton : $notSelectedButton;
        ?> is-not-ajax">
                <i class="<?php echo $cssClass; ?>"></i>
                <span class="hide-on-820">
                    <?php
             (
                 isset($specialLabels[$action]) ?
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