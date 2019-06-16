<?php

class UpdateCheckController extends Controller {

    public function patchCheck() {
        HTMLResult(Template::executeModuleTemplate("core_package_manager", "patch_check.php"));
    }

}
