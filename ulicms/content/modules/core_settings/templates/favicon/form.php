<?php

use UliCMS\Utils\File;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("favicon")) {
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("design"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <?php
    if (isset($_GET["error"])) {
        ?>
        <div class="alert alert-danger">
            <?php echo translate(_esc($_GET["error"])); ?>
        </div>
    <?php } ?>
    <h1><?php translate("favicon"); ?></h1>
    <p><?php
        translate("favicon_infotext");
        ?>
    </p>
    <form enctype="multipart/form-data" action="index.php?action=favicon"
          method="post">
        <input type="hidden" name="sClass" value="FaviconController"> <input
            type="hidden" name="sMethod" value="doUpload">
            <?php
            csrf_token_html();
            ?>
        <table style="height: 250px">
            <tr>
                <td><strong><?php
                        translate("current_favicon");
                        ?>
                    </strong></td>
                <td><?php
                    $favicon_path = ULICMS_DATA_STORAGE_ROOT . "/content/images/favicon.ico";
                    $favicon_url = defined("ULICMS_DATA_STORAGE_URL") ? ULICMS_DATA_STORAGE_URL . "/content/images/favicon.ico" : "../content/images/favicon.ico";
                    if (file_exists($favicon_path)) {
                        $favicon_url .= "?time=" . File::getLastChanged($favicon_path);
                        echo '<img class="website_favicon" src="' . $favicon_url . '" alt="' . Settings::get("homepage_title") . '"/>';
                    }
                    ?>
                </td>

            </tr>
            <tr>

                <td><label for="high_resolution"><strong><?php translate("high_resolution"); ?></strong></label>
                </td>
                <td><input type="checkbox" id="high_resolution"
                           name="high_resolution" value="high_resolution"></td>
            </tr>
            <tr>
                <td width=480><strong><?php
                        translate("upload_new_favicon");
                        ?>
                    </strong></td>
                <td><input name="favicon_upload_file" type="file"> <br /></td>

            </tr>
            <tr>
                <td></td>
                <td class="text-center"><button type="submit"
                                                class="btn btn-primary"><?php translate("upload"); ?></td>
            </tr>
        </table>
    </form>

    <?php
} else {
    noPerms();
}
