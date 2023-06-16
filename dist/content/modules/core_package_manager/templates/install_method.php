<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

?>
<p>
    <a
        href="<?php echo \App\Helpers\ModuleHelper::buildMethodCallUrl(PackageController::class, 'redirectToPackageView'); ?>"
        class="btn btn-light btn-back is-not-ajax"><i class="fa fa-arrow-left" aria-hidden="true"></i>
        <?php translate('back'); ?></a>
</p>
<h1><?php translate('install_package'); ?></h1>
<p>
    <a href="?action=upload_package" class="btn btn-light is-ajax">
        <i class="fa fa-upload"></i> <?php translate('upload_file'); ?>
    </a>
</p>
<p>
    <a href="?action=available_modules" class="btn btn-light"><i class="fas fa-box"></i> <?php translate('from_the_package_source'); ?>
    </a>
</p>
<p>
    <a href="http://extend.ulicms.de" class="btn btn-light"
        target="_blank"><i class="fas fa-store-alt"></i> UliCMS eXtend</a>
</p>
