<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

define('MODULE_ADMIN_HEADLINE', get_translation('update_manager'));
define('MODULE_ADMIN_REQUIRED_PERMISSION', 'install_packages');

function update_manager_admin(): void {
    $updates = UpdateManager::getAllUpdateablePackages();
    $i = 0;
    ?>
    <form action="#" id="update-manager" method="get">

        <?php
        if (count($updates) > 0) {
            ?>
            <p><?php
                if (count($updates) == 1) {
                    translate('UPDATES_AVAILABLE_SINGULAR');
                } else {
                    translate('UPDATES_AVAILABLE_MULTIPLE', [
                        '%count%' => count($updates)
                    ]);
                }
            ?></p>
            <p>
                <input id="checkall" type="checkbox" class="checkall" checked> <label
                    for="checkall"><?php translate('select_all'); ?> </label>
            </p>
            <?php
            foreach ($updates as $update) {
                $i++;
                ?>
                <input type="checkbox" class="package" id="update_<?php echo $i; ?>"
                       name="updates[]" value="<?php Template::escape($update); ?>" checked> <label
                       for="update_<?php echo $i; ?>"><?php Template::escape($update); ?></label>
                <br />
            <?php }
            ?>
            <p>
                <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> <?php translate('install_updates'); ?></button>
            </p>
            <?php
        } else {
            ?>
            <p><?php translate('NO_UPDATES_AVAILABLE'); ?></p>
        <?php }
        ?>
        <span id="translation_please_select_packages"
              data-translation="<?php translate('please_select_packages'); ?>"></span>
    </form>
    <?php
    enqueueScriptFile(
        \App\Helpers\ModuleHelper::buildRessourcePath(
            'update_manager',
            'scripts/update_manager.js'
        )
    );
    combinedScriptHtml();
}
