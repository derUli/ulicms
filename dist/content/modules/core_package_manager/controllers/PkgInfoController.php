<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Packages\SinPackageInstaller;

class PkgInfoController extends \App\Controllers\Controller {
    public function installPost(): void {
        // TODO: handle incomplete requests and errors
        if (! empty($_REQUEST['file'])) {
            $file = basename($_POST['file']);
            $this->_installPost($file);
            Response::redirect(
                \App\Helpers\ModuleHelper::buildActionURL(
                    'sin_package_install_ok',
                    "file={$file}"
                )
            );
        }
    }

    public function _installPost($file): bool {
        $path = \App\Utils\Path::resolve("ULICMS_TMP/{$file}");
        $pkg = new SinPackageInstaller($path);
        if (is_file($path)) {
            return $pkg->installPackage();
        }
        return false;
    }
}
