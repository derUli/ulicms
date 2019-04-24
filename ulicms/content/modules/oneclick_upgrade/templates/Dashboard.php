<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("update_system")) {
    $version = ControllerRegistry::get("CoreUpgradeController")->checkForUpgrades();
    if ($version) {
        ?>
        <h2 class="accordion-header"><?php translate("ONECLICK_UPGRADE"); ?></h2>
        <div class="accordion-content">
            <p><?php translate("an_upgrade_is_available", array("%version%" => $version)); ?> </p>
            <p><a href="<?= ModuleHelper::buildActionURL("UpgradeCheck"); ?>" class="btn btn-info">
                    <i class="fas fa-info-circle"></i> <?php translate("show_more"); ?></a></p>
        </div>
        <?php
    }
}