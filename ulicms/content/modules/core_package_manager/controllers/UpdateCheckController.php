<?php

declare(strict_types=1);

class UpdateCheckController extends Controller {

    public function patchCheck(): void {
        $html = $this->_patchCheck();
        HTMLResult($html);
    }

    public function _patchCheck(): string {
        return Template::executeModuleTemplate(
                        "core_package_manager",
                        "patch_check.php"
        );
    }

}
