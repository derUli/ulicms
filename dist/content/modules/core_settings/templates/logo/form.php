<?php
use App\Helpers\ImagineHelper;

use function App\HTML\imageTag;

$controller = new LogoController();

$permissionChecker = new ACL();

$logoUrl = "../content/images/" . Settings::get("logo_image");

$logoStoragePath = ULICMS_ROOT . "/content/images/" . Settings::get("logo_image");

if ($permissionChecker->hasPermission("logo")) {
    ?>
    <p>
        <a
            href="<?php echo ModuleHelper::buildActionURL("design"); ?>"
            class="btn btn-default btn-back is-not-ajax">
            <i class="fa fa-arrow-left"></i> 
            <?php translate("back") ?>
        </a>
    </p>
    <h1><?php translate("upload_new_logo"); ?></h1>
    <p>
        <?php translate("logo_infotext"); ?>
    </p>
    <form enctype="multipart/form-data" action="index.php" method="post">
        <?php csrf_token_html(); ?>
        <input type="hidden" name="sClass" value="LogoController" /> <input
            type="hidden" name="sMethod" value="upload" />
        <table style="height: 250px">

            <?php if ($controller->_hasLogo()) {
                ?>

                <tr>
                    <td><strong><?php translate("your_logo"); ?>
                        </strong></td>
                    <td>
                        <div id="logo-wrapper">
                            <?php
                            if (is_file($logoStoragePath)) {
                                echo imageTag(
                                    $logoUrl,
                                    [
                                        "alt" => Settings::get("homepage_title"),
                                        "class" => "img-responsive"
                                    ]
                                );
                                ?>
                                <div class="voffset2">
                                    <button
                                        type="button"
                                        class="btn btn-default"
                                        id="delete-logo"
                                        data-url="<?php
                                        echo ModuleHelper::buildMethodCallUrl(
                                    LogoController::class,
                                    "deleteLogo"
                                );
                                ?>
                                        "
                                        >
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                        <?php translate("delete_logo"); ?>
                                    </button>
                                <?php }
                            ?>
                            </div>
                        </div>
                        <img
                            id="delete-logo-loading"
                            src="gfx/loading.gif"
                            alt="<?php translate("loading_alt"); ?>"
                            style="display: none;"
                            >
                    </td>
                </tr>
            <?php }
            ?>
            <tr>
                <td width="480"><strong><?php translate("upload_new_logo"); ?>
                    </strong></td>
                <td>
                    <input name="logo_upload_file" type="file" accept="<?php echo ImagineHelper::ACCEPT_MIMES; ?>">
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="text-center"><button type="submit"
                                                class="btn btn-primary voffset2">
                        <i class="fa fa-upload"></i> <?php translate("upload"); ?></button></td>
            </tr>
        </table>
    </form>
    <?php
    $translation = new JSTranslation();
    $translation->addKey("delete_logo");
    $translation->addKey("logo_deleted");
    $translation->render();

    enqueueScriptFile(
        ModuleHelper::buildRessourcePath(
            "core_settings",
            "js/logo.js"
        )
    );
    combinedScriptHtml();
} else {
    noPerms();
}
