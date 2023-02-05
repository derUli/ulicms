<?php

declare(strict_types=1);

class PkgInfoController extends Controller {

    public function installPost(): void {
        // TODO: handle incomplete requests and errors
        if (StringHelper::isNotNullOrEmpty($_REQUEST["file"])) {
            $file = basename($_POST["file"]);
            $this->_installPost($file);
            Request::redirect(
                    ModuleHelper::buildActionURL(
                            "sin_package_install_ok",
                            "file=$file"
                    )
            );
        }
    }

    public function _installPost($file): bool {
        $path = Path::resolve("ULICMS_TMP/$file");
        $pkg = new SinPackageInstaller($path);
        if (file_exists($path)) {
            return $pkg->installPackage();
        }
        return false;
    }

}
