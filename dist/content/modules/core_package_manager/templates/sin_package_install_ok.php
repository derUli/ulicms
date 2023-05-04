<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

$file = Template::getEscape($_REQUEST['file'] ?? '-');
?>
<h1><?php translate('install_package'); ?></h1>
<p><?php translate('PACKAGE_SUCCESSFULLY_UPLOADED', ['%file%' => $file]); ?></p>
<p>
    <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('upload_package'); ?>"
        class="btn btn-warning"><i class="fas fa-box"></i> <?php translate('install_another_package'); ?></a>
</p>