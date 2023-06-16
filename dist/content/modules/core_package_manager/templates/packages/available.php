<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Translations\JSTranslation;

?>
<p>
    <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('install_method'); ?>"
        class="btn btn-light btn-back is-not-ajax"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?php translate('back'); ?></a>
</p>
<h1><?php translate('available_packages'); ?></h1>
<div id="loadpkg">
    <?php require 'inc/loadspinner.php'; ?>
</div>
<div id="pkglist" data-url="<?php echo \App\Helpers\ModuleHelper::buildMethodCallUrl(PackageController::class, 'availablePackages'); ?>"></div>
<?php
$jsTranslation = new JSTranslation([], 'AvailablePackageTranslation');
$jsTranslation->addKey('ASK_FOR_INSTALL_PACKAGE');
$jsTranslation->render();

enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath('core_package_manager', 'js/available.js'));
combinedScriptHtml();
