<?php

use UliCMS\Packages\PatchManager;

$patchManager = new PatchManager();
$availablePatches = $patchManager->getAvailablePatches();

$permissionChecker = new ACL();
// no patch check in google cloud
$runningInGoogleCloud = class_exists("GoogleCloudHelper") ? GoogleCloudHelper::isProduction() : false;

if ($permissionChecker->hasPermission("update_system") && !$runningInGoogleCloud) {
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("home"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?php translate("back") ?></a>
    </p>
    <h1><?php translate("available_patches"); ?></h1>
    <div class="alert alert-info"><?php translate("patches_will_fix_errors"); ?></div>
    <?php
    if (count($availablePatches) === 0) {
        ?>
        <div class="alert alert-danger">
        <?php translate("no_patches_available"); ?></div>
        <?php
    } else {
        ?>
        <form action="index.php?action=install_patches" method="post">
            <?php csrf_token_html(); ?>
            <?php
            foreach ($availablePatches as $patch) {
                ?>
                <p>
                    <label>
                        <input name="patches[]" type="checkbox" checked="checked"
                               value="<?php esc($patch->toLine()); ?>">
                        <strong><?php esc($patch->name); ?></strong>
                        <br />
            <?php esc($patch->description); ?>
                    </label>
                </p>
                <?php
            }
        }
        ?>
        <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> <?php translate("install_selected_patches"); ?></button>
        <button type="button"
                onclick="window.open('?action=help&help=patch_install');"
                class="btn btn-info"><i class="fa fa-question-circle" aria-hidden="true"></i> <?php translate("help"); ?></button>
    </form>
    <?php
} else {
    noPerms();
}
