<?php

use App\Security\PermissionChecker;
use App\Constants\RequestMethod;
use App\Packages\Theme;

$permissionChecker = new PermissionChecker(get_user_id());

if ($permissionChecker->hasPermission('list_packages')) {
    $manager = new ModuleManager();
    $manager->sync();
    $modules = $manager->getAllModules();
    $anyEmbedModules = count(ModuleHelper::getAllEmbedModules()) > 0;
    ?>

    <div class="btn-toolbar">
        <a href="?action=install_method" class="btn btn-warning is-ajax"><i
                class="fa fa-plus"></i> <?php translate('install_package'); ?></a>
    </div>
    <h2><?php translate('installed_modules'); ?></h2>
    <div class="scroll">
        <table class="tablesorter">
            <thead>
                <tr>
                    <th><?php translate('module'); ?></th>
                    <th class="hide-on-mobile"><?php translate('version'); ?></th>
                    <?php if ($anyEmbedModules) { ?>
                        <th><?php translate('shortcode'); ?></th>
                    <?php } ?>
                    <th class="actions no-sort"><?php translate('actions'); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ($modules as $module) {
                    $hasAdminPage = ($module->hasAdminPage());
                    $isEnabled = $module->isEnabled();
                    $adminPermission = getModuleMeta(
                        $module->getName(),
                        'admin_permission'
                    );
                    $userIsPermitted = (
                        $adminPermission &&
                        $permissionChecker->hasPermission($adminPermission)
                    )
                            || (! $adminPermission
                            );
                    $btnClass = ($hasAdminPage && $userIsPermitted) ?
                            'btn btn-primary' :
                            'btn btn-default disabled has-no-settings';
                    ?>
                    <tr>
                        <td><a
                                href="<?php esc(ModuleHelper::buildAdminURL($module->getName())); ?>"
                                class="<?php esc($btnClass); ?>"
                                <?php
                                if (! $hasAdminPage || ! $isEnabled) {
                                    echo 'disabled';
                                }
                    ?>
                                data-btn-for="<?php esc($module->getName()); ?>"><i
                                    class="fas fa-tools"></i> <?php esc($module->getName()); ?> </a>
                                <?php if (! $userIsPermitted && $hasAdminPage) { ?>
                                <i class="fas fa-lock pull-right"
                                   title="<?php translate('no_permission'); ?>"></i>
                               <?php } ?>
                        </td>
                        <td class="hide-on-mobile"><?php esc(getModuleMeta($module->getName(), 'version')); ?></td>
                        <?php if ($anyEmbedModules) { ?>
                            <td><?php
                    if ($module->isEmbedModule()) {
                        echo App\HTML\Input::textBox(
                            '',
                            $module->getShortCode(),
                            'text',
                            [
                                'readonly' => 'readonly',
                                'class' => 'select-on-click'
                            ]
                        );
                    }
                            ?></td>

                        <?php } ?>
                        <td class="actions">
                            <div class="btn-toolbar">
                                <span class="btn btn-info btn-sm remote-alert icon"
                                      title="<?php translate('info'); ?>"
                                      data-url="<?php echo ModuleHelper::buildMethodCallUrl(PackageController::class, 'getModuleInfo', "name={$module->getName()}"); ?>"><i
                                        class="fas fa-info-circle"></i> </span>
                                    <?php
                                $canToggleModule = (getModuleMeta($module->getName(), 'source') != 'core' && $permissionChecker->hasPermission('enable_disable_module'));
                    echo ModuleHelper::buildMethodCallForm(PackageController::class, 'toggleModule', [
                        'name' => $module->getName()
                            ], RequestMethod::POST, [
                        'class' => 'inline-block toggle-module-form',
                        'data-confirm-message' => get_translation('uninstall_module_x', [
                            '%name%' => $module->getName()
                        ])
                    ]);
                    ?>
                                <button type="submit" <?php
                                if (! $canToggleModule) {
                                    echo 'disabled';
                                }
                    ?> class="btn btn-success bt-sm icon btn-disable" style="<?php
                            if (! $module->isEnabled()) {
                                echo 'display:none';
                            }
                    ?>" title="<?php translate('disable_module'); ?>"><?php translate('on'); ?></button>
                                <button type="submit"  <?php
                                if (! $canToggleModule) {
                                    echo 'disabled';
                                }
                    ?> class="btn btn-danger bt-sm icon btn-enable" style="<?php
                            if ($module->isEnabled()) {
                                echo 'display:none';
                            }
                    ?>" title="<?php translate('enable_module'); ?>"><?php translate('off'); ?></button>
                                        <?php echo ModuleHelper::endForm(); ?>
                                        <?php
                    if ($permissionChecker->hasPermission('remove_packages') && getModuleMeta($module->getName(), 'source') != 'core') {
                        echo ModuleHelper::buildMethodCallForm(PackageController::class, 'uninstallModule', [
                            'name' => $module->getName()
                                ], RequestMethod::POST, [
                            'class' => 'inline-block uninstall-form',
                            'data-confirm-message' => get_translation('uninstall_module_x', [
                                '%name%' => $module->getName()
                            ])
                        ]);
                        ?>
                                    <button type="submit" class="btn btn-danger bt-sm icon"
                                            title="<?php translate('uninstall'); ?>">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                    <?php
                                    echo ModuleHelper::endForm();
                    }
                    ?>
                            </div>
                        </td>
                    </tr>
                <?php }
                ?>
            </tbody>
        </table>
    </div>
    <?php $themes = getAllThemes(); ?>
    <h2><?php translate('installed_designs'); ?></h2>
    <div class="scroll">
        <table
            class="tablesorter"
            id="design-table">
            <thead>
                <tr>
                    <th><?php translate('design'); ?></th>
                    <th class="hide-on-mobile"><?php translate('version'); ?></th>
                    <th class="actions no-sort"><?php translate('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($themes as $theme) {
                    $theTheme = new Theme($theme);

                    $inGeneralUse = (Settings::get('theme') == $theme);
                    $inMobileUse = (Settings::get('mobile_theme') == $theme)
                    ?>
                    <tr>
                        <td><?php esc($theme); ?></td>
                        <td class="hide-on-mobile">
                            <?php esc(getThemeMeta($theme, 'version')); ?>
                        </td>
                        <td class="actions">
                            <div class="btn-toolbar">
                                <?php
                                $colorClasses = $inGeneralUse ?
                                        'btn-success' : 'btn-danger';
                    ?>
                                <span
                                    class="btn <?php echo $colorClasses; ?>
                                    btn-sm icon default-theme-icon"
                                    title="<?php translate('set_as_default_theme'); ?>"
                                    data-theme="<?php esc($theme); ?>"
                                    data-url="<?php
                        echo ModuleHelper::buildMethodCallUrl(
                            DesignSettingsController::class,
                            'setDefaultTheme',
                            "name={$theme}"
                        );
                    ?>"
                                    >
                                    <i class="fa fa-desktop"></i>
                                </span>
                                <?php
                                $colorClasses = $inMobileUse ?
                        'btn-success ' : 'btn-danger';
                    ?>
                                <span
                                    class="btn <?php echo $colorClasses; ?>
                                    btn-sm icon default-mobile-theme-icon"
                                    title="<?php translate('set_as_mobile_default_theme'); ?>"
                                    data-theme="<?php esc($theme); ?>"
                                    data-url="<?php
                        echo ModuleHelper::buildMethodCallUrl(
                            DesignSettingsController::class,
                            'setDefaultMobileTheme',
                            "name={$theme}"
                        );
                    ?>"
                                    >
                                    <i class="fas fa-mobile-alt"></i>
                                </span>
                                <span class="btn btn-info btn-sm remote-alert icon"
                                      title="<?php translate('info'); ?>"
                                      data-url="<?php
                      echo ModuleHelper::buildMethodCallUrl(
                          'PackageController',
                          'getThemeInfo',
                          "name={$theme}"
                      );
                    ?>"
                                      >
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                </span>
                                <?php if ($theTheme->hasScreenshot()) {
                                    ?>
                                    <span class="btn btn-info btn-sm remote-alert icon"
                                          title="<?php translate('show_preview'); ?>"
                                          data-url="<?php
                                          echo ModuleHelper::buildMethodCallUrl(
                                              DesignSettingsController::class,
                                              'themePreview',
                                              "theme={$theme}"
                                          );
                                    ?>"
                                          >
                                        <i class="far fa-image"></i>
                                    </span>
                                <?php }
                                ?>
                                <?php
                                if ($permissionChecker->hasPermission('remove_packages') && getModuleMeta($module->getName(), 'source') != 'core') {
                                    echo ModuleHelper::buildMethodCallForm(PackageController::class, 'uninstallTheme', [
                                        'name' => $theme
                                            ], RequestMethod::POST, [
                                        'class' => 'inline-block uninstall-form',
                                        'data-confirm-message' => get_translation('uninstall_theme_x', [
                                            '%name%' => $theme
                                        ])
                                    ]);
                                    ?>
                                    <button type="submit" class="btn btn-danger btn-sm icon"
                                            title="<?php translate('uninstall'); ?>">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                    <?php
                                    echo ModuleHelper::endForm();
                                }
                    ?>
                            </div>
                        </td>
                    </tr>
                <?php }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    enqueueScriptFile(ModuleHelper::buildRessourcePath(PackageController::MODULE_NAME, 'js/list.js'));
    combinedScriptHtml();
    ?>
    <?php
} else {
    noPerms();
}
