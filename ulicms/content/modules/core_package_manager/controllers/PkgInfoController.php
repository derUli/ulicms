<?php

declare(strict_types=1);

class PkgInfoController extends Controller {

    public function installPost(): void {
        if (StringHelper::isNotNullOrEmpty($_REQUEST["file"])) {
            $file = basename($_POST["file"]);
            $path = Path::resolve("ULICMS_TMP/$file");
            $pkg = new SinPackageInstaller($path);
            if (file_exists($path)) {
                $pkg->installPackage();
            }
            Request::redirect(
                    ModuleHelper::buildActionURL(
                            "sin_package_install_ok", "file=$file"
                    )
            );
        }
    }

}
