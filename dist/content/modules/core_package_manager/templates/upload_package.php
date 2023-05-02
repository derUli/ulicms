<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Packages\PackageManager;
use App\Security\Permissions\PermissionChecker;

$permissionChecker = PermissionChecker::fromCurrentUser();

// TODO: Refactor this
// Move logic to controller
// don't use so much nested if-statements

if (! $permissionChecker->hasPermission('install_packages')) {
    noPerms();
} else {
    ?>
    <p>
        <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('install_method'); ?>"
           class="btn btn-default btn-back is-ajax">
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
            <?php translate('back'); ?></a>
    </p>
    <?php
    $temp_folder = ULICMS_ROOT . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'tmp';
    if (! empty($_POST)) {
        if (count($_FILES) > 0) {
            $file_in_tmp = $temp_folder . DIRECTORY_SEPARATOR . $_FILES['file']['name'];
            if (move_uploaded_file($_FILES['file']['tmp_name'], $file_in_tmp)) {
                if (str_ends_with($file_in_tmp, '.tar.gz')) {
                    $pkg = new PackageManager();
                    if ($pkg->installPackage($file_in_tmp, false)) {
                        @unlink($file_in_tmp);
                        echo "<p style='color:green'>" . get_translation('PACKAGE_SUCCESSFULLY_UPLOADED', [
                            '%file%' => $_FILES['file']['name']
                        ]) . '</p>';
                    } else {
                        echo "<p style='color:red'>" . get_translation('installation_failed', [
                            '%file%' => $_FILES['file']['name']
                        ]) . '</p>';
                    }
                } elseif (str_ends_with($file_in_tmp, '.sin')) {
                    $url = '?action=pkginfo&file=' . basename($file_in_tmp);
                    Response::javascriptRedirect($url);
                } else {
                    echo "<p style='color:red'>" . get_translation('not_supported_format') . '</p>';
                }
            } else {
                echo "<p style='color:red'>" . get_translation('upload_failed') . '</p>';
            }
        }
    }
    ?>
    <h1><?php translate('upload_package'); ?></h1>
    <form action="?action=upload_package" enctype="multipart/form-data"
          method="post">
              <?php csrf_token_html(); ?>
        <p>
            <input
                type="file"
                name="file"
                class="form-control"
                required 
                accept=".sin,.tar.gz"
                >
        </p>
        <p>
            <button type="submit" class="btn btn-warning">
                <i class="fa fa-upload" aria-hidden="true"></i>
                <?php translate('install_package'); ?>
            </button>
        </p>
    </form>
    <?php
}
