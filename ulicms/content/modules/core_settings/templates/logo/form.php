<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("logo")) {
    ?>
    <p>
        <a
            href="<?php echo ModuleHelper::buildActionURL("design"); ?>"
            class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <h1><?php translate("upload_new_logo"); ?></h1>
    <p>
        <?php translate("logo_infotext"); ?>
    </p>
    <form enctype="multipart/form-data" action="index.php" method="post">
        <?php csrf_token_html(); ?>
        <input type="hidden" name="sClass" value="LogoUploadController" /> <input
            type="hidden" name="sMethod" value="upload" />
        <table style="height: 250px">
            <tr>
                <td><strong><?php translate("your_logo"); ?>
                    </strong></td>
                <td><?php
                    if (defined("ULICMS_DATA_STORAGE_URL")) {
                        $logo_path = ULICMS_DATA_STORAGE_URL . "/content/images/" . Settings::get("logo_image");
                    } else {
                        $logo_path = "../content/images/" . Settings::get("logo_image");
                    }
                    $logo_storage_path = ULICMS_DATA_STORAGE_ROOT . "/content/images/" . Settings::get("logo_image");

                    if (file_exists($logo_storage_path)) {
                        echo '<img class="website_logo" src="' . $logo_path . '" alt="' . Settings::get("homepage_title") . '"/>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td width="480"><strong><?php translate("upload_new_logo"); ?>
                    </strong></td>
                <td>
                    <input name="logo_upload_file" type="file" accept="image/*">
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
} else {
    noPerms();
}
