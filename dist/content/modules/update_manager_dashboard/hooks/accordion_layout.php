<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;

$permissionChecker = PermissionChecker::fromCurrentUser();
if ($permissionChecker->hasPermission('install_packages')) {
    ?>
    <div id="update-manager-dashboard-container" style="display: none">
        <h2 class="accordion-header"><?php translate('update_manager'); ?></h2>
        <div class="accordion-content">
            <p><?php translate('PACKAGE_UPDATES_ARE_AVAILABLE'); ?></p> <a
                href="<?php echo \App\Helpers\ModuleHelper::buildAdminURL('update_manager'); ?>" class="btn btn-info"><?php translate('show_available_updates'); ?>
            </a>
        </div>
    </div>
    <?php
    enqueueScriptFile(getModulePath('update_manager_dashboard') . 'scripts/update_manager_dashboard.js');
    combinedScriptHtml();
}
