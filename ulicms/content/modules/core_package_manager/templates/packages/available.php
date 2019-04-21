<?php

use UliCMS\Security\PermissionChecker;

$permissionChecker = new PermissionChecker(get_user_id());
if (!$permissionChecker->hasPermission("install_packages")) {
    noPerms();
} else {
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("install_method"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?php translate("back") ?></a>
    </p>
    <h1><?php translate("available_packages") ?></h1>
    <div id="loadpkg">
        <?php require "inc/loadspinner.php"; ?>
    </div>
    <div id="pkglist" data-url="<?php echo ModuleHelper::buildMethodCallUrl(PackageController::class, "availablePackages"); ?>"></div>
    <?php
    $jsTranslation = new JSTranslation(array(), "AvailablePackageTranslation");
    $jsTranslation->addKey("ASK_FOR_INSTALL_PACKAGE");
    $jsTranslation->render();
    
    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_package_manager", "js/available.js"));
    combinedScriptHtml();
}