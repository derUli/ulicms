<?php

declare(strict_types=1);

class UpdateCheckController extends Controller {

    public function patchCheck(): void {
        HTMLResult(
                Template::executeModuleTemplate(
                        "core_package_manager",
                        "patch_check.php"
                )
        );
    }

}
