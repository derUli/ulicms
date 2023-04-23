<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;

$permissionChecker = PermissionChecker::fromCurrentUser();

/**
 * @var CoreUpgradeController|null
 */
$controller = ControllerRegistry::get('CoreUpgradeController');

if ($controller && $permissionChecker->hasPermission('update_system')) {
    $version = $controller->checkForUpgrades();

    if ($version) {
        ?>
        <h2 class="accordion-header"><?php translate('ONECLICK_UPGRADE'); ?></h2>
        <div class="accordion-content">
            <p><?php translate('an_upgrade_is_available', ['%version%' => $version]); ?> </p>
            <p><a href="<?= ModuleHelper::buildActionURL('UpgradeCheck'); ?>" class="btn btn-info">
                    <i class="fas fa-info-circle"></i> <?php translate('show_more'); ?></a></p>
        </div>
        <?php
    }
}
