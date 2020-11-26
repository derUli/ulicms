<?php

use UliCMS\Packages\PatchManager;

$permissionChecker = new ACL();
if (!$permissionChecker->hasPermission("upload_patches")) {
    noPerms();
} else {
    $redirect = false;
    if (isset($_POST ["upload_patch"]) and isset($_FILES ['file'] ['tmp_name'])
            and endsWith($_FILES ['file'] ['name'], ".zip")) {
        $patchManager = new PatchManager();
        if ($patchManager->installPatch(
            $_POST ["name"],
            $_POST ["description"],
            $_FILES ['file']['tmp_name']
        )
        ) {
            $redirect = true;
        }
    }

    $backUrl = ModuleHelper::buildMethodCallUrl(PackageController::class, "redirectToPackageView");

    if ($redirect) {
        Response::javascriptRedirect($backUrl);
    } ?>
    <p>
        <a
            href="<?php esc($backUrl); ?>"
            class="btn btn-default btn-back is-not-ajax"
            >
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
    <?php translate("back") ?></a>
    </p>
    <h1><?php translate("install_patch_from_file"); ?></h1>
    <form enctype="multipart/form-data"
          action="index.php?action=upload_patches" method="POST">
    <?php csrf_token_html(); ?>
        <p>
            <strong><?php translate("name"); ?></strong> <br />
            <input
                type="text"
                name="name"
                value=""
                required
                class="form-control"
                />
        </p>
        <p>
            <strong><?php translate("description"); ?></strong> <br />
            <input
                type="text"
                name="description"
                value=""
                required 
                class="form-control"
                />
        </p>
        <p>
            <strong><?php translate("file"); ?></strong> <br />
            <input
                name="file"
                type="file"
                required
                class="form-control"
                accept=".zip,application/zip"
                />
        </p>
        <p>
            <button type="submit" class="btn btn-warning" name="upload_patch">
                <i class="fa fa-upload" aria-hidden="true"></i>
    <?php translate("install_patch"); ?></button>
        </p>
    </form>
    <?php
}
