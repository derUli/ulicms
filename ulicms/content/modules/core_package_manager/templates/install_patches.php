<?php

use App\Exceptions\CorruptDownloadException;
use UliCMS\Packages\Patch;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("update_system")) {
    // No time limit
    @set_time_limit(0);
    ?>
    <p>
        <a
            href="<?php echo ModuleHelper::buildActionURL("available_patches"); ?>"
            class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?php translate("back") ?></a>
    </p>
    <h1><?php translate("install_patches"); ?></h1>
    <?php
    $patches = $_POST["patches"];

    if (count($patches) <= 0) {
        translate("no_patches_selected");
    } else {
        foreach ($patches as $patch) {
            $patchObject = Patch::fromLine($patch);

            try {
                $success = $patchObject->install();

                if ($success) {
                    echo '<p style="color:green">' . _esc($patchObject->name) . " " .
                    get_translation("was_successfully_installed") . '</p>';
                } else {
                    echo '<p style="color:red">' .
                    get_translation("installation_of") . " " .
                    _esc($patchObject->name) . " " . get_Translation("is_failed") .
                    "</p>";
                }
            } catch (CorruptDownloadException $e) {
                echo '<p>' . get_secure_translation("download_of_x_failed", array(
                    "%item%" => $patchObject->name
                )) . '</p>';
            }
            fcflush();
        }
    }
} else {
    noPerms();
}
