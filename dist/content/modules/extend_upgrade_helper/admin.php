<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use function App\HTML\icon;

define('MODULE_ADMIN_HEADLINE', get_translation('extend_upgrade_helper'));

function extend_upgrade_helper_admin(): void {
    $controller = ControllerRegistry::get('ExtendUpgradeHelper');
    $modules = $controller->getModules();
    ?>
    <div class="alert alert-info">
        <?php translate('EXTEND_UPGRADE_HELPER_INSTRUCTION'); ?></div>
    <?php
    if (count($modules) > 0) {
        ?>
        <ol>
            <?php foreach ($modules as $module) { ?>
                <li>
                    <a href="<?php Template::escape($module->url); ?>" target="_blank"><?php Template::escape($module->name); ?>
                        <?php Template::escape($module->version); ?></a>
                    <?php
                    if ($module->updateAvailable) {
                        echo ' ' . icon('fas fa-download text-red');
                    }
                ?>

                </li>
            <?php } ?>
        </ol>
        <?php
    } else {
        ?>
        <div class="alert alert-success alert-dismissable fade in">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?php translate('no_extend_modules'); ?>
        </div>
        <?php
    }
}
