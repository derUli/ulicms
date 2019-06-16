<?php

class PkgInfoController extends Controller {

    public function installPost() {
        if (StringHelper::isNotNullOrEmpty($_REQUEST["file"])) {
            $file = basename($_POST["file"]);
            $path = Path::resolve("ULICMS_TMP/$file");
            $pkg = new SinPackageInstaller($path);
            if (is_file($path)) {
                $pkg->installPackage();
                @unlink($path);
            }
            Request::redirect(ModuleHelper::buildActionURL("sin_package_install_ok", "file=$file"));
        }
    }

}
