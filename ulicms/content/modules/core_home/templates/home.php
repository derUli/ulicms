<?php

use UliCMS\Helpers\NumberFormatHelper;

$permissionChecker = new ACL();

$controller = ControllerRegistry::get();
$model = $controller->getModel();

// no patch check in google cloud
$runningInGoogleCloud = class_exists("GoogleCloudHelper") ? GoogleCloudHelper::isProduction() : false;
if ($permissionChecker->hasPermission("dashboard")) {
    ?>
    <p>
        <?php
        $str = get_translation("hello_name");
        $str = str_ireplace("%firstname%", $_SESSION["firstname"], $str);
        $str = str_ireplace("%lastname%", $_SESSION["lastname"], $str);
        esc($str);
        ?> </p>
    <p>
        <a
            href="?action=admin_edit&id=<?php echo $_SESSION["login_id"] ?>&ref=home"
            class="btn btn-default"><i class="fas fa-user"></i> <?php translate("edit_profile"); ?></a>
    </p>
    <div id="accordion-container">
        <?php
        $motd = get_lang_config("motd", getSystemLanguage());
        if ($motd or strlen($motd) > 10) {
            ?>
            <h2 class="accordion-header">
                <?php translate("motd"); ?></h2>
            <div class="accordion-content">
                <?php
                echo $motd;
                ?>
            </div>
        <?php } ?>
        <div id="patch-notification" style="display: none;"
             data-url="<?php echo ModuleHelper::buildMethodCallUrl(UpdateCheckController::class, "patchCheck"); ?>">
            <h2 class="accordion-header">
                <?php translate("there_are_patches_available"); ?>
            </h2>
            <div class="accordion-content" id="patch-message"></div>
        </div>
        <?php
        $pi = ULICMS_DATA_STORAGE_ROOT . "/post-install.php";
        if (is_writable($pi)) {
            ?>
            <h2 class="accordion-header"><?php translate("unfinished_package_installations"); ?></h2>
            <div class="accordion-content">
                <a
                    href="<?php echo ModuleHelper::buildActionURL("do_post_install"); ?>">
                    <?php translate("there_are_unfinished_package_installations"); ?></a>
            </div>
        <?php } ?>
        <?php
        if (!Settings::get("disable_ulicms_newsfeed")) {
            ?>
            <div class="has-ajax-content"
                 data-url="<?php echo ModuleHelper::buildMethodCallUrl(HomeController::class, "newsfeed") ?>">

                <h2 class="accordion-header" >
                    <?php translate("ulicms_news"); ?></h2>
                <div class="accordion-content">
                    <?php require "inc/loadspinner.php"; ?>
                </div>
            </div>
        <?php } ?>
        <?php if ($permissionChecker->hasPermission("pages_show_positions")) { ?>
            <h2 class="accordion-header"><?php translate("helper_utils"); ?></h2>
            <div class="accordion-content">
                <form action="#" class="checkbox">
                    <label>
                        <input name="show_positions" id="show_positions" type="checkbox"
                               class="js-switch"
                               data-url="<?php esc(ModuleHelper::buildMethodCallUrl(PageController::class, "toggleShowPositions")); ?>" value="1"
                               <?php
                               if (Settings::get("user/" . get_user_id() . "/show_positions"))
                                   echo "checked";
                               ?>>
                        <?php translate("show_positions_in_menus"); ?></label>
                    </label>
                </form>
            </div>
        <?php } ?>
        <div class="has-ajax-content" data-url="<?php echo ModuleHelper::buildMethodCallUrl(HomeController::class, "statistics"); ?>">
            <h2 class="accordion-header">
                <?php translate("statistics"); ?>
            </h2>
            <div class="accordion-content">
                <?php require "inc/loadspinner.php"; ?>
            </div>
        </div>
        <div class="has-ajax-content always-update" data-url="<?php echo ModuleHelper::buildMethodCallUrl(HomeController::class, "onlineUsers"); ?>">
            <h2 class="accordion-header">
                <?php translate("online_now"); ?>
            </h2>
            <div class="accordion-content">
                <?php require "inc/loadspinner.php"; ?>
            </div>
        </div>
        <div class="has-ajax-content" data-url="<?php echo ModuleHelper::buildMethodCallUrl(HomeController::class, "topPages"); ?>">
            <h2 class="accordion-header">
                <?php translate("top_pages"); ?>
            </h2>
            <div class="accordion-content">
                <?php require "inc/loadspinner.php"; ?>
            </div>
        </div>
        <div class="has-ajax-content" data-url="<?php echo ModuleHelper::buildMethodCallUrl(HomeController::class, "lastUpdatedPages"); ?>">
            <h2 class="accordion-header"><?php translate("last_changes"); ?></h2>
            <div class="accordion-content">
                <?php require "inc/loadspinner.php"; ?>
            </div>
        </div>
        <?php
        do_event("accordion_layout");
        ?>
    </div>
    <?php
    if (!$runningInGoogleCloud) {
        enqueueScriptFile(ModuleHelper::buildModuleRessourcePath("core_home", "js/dashboard.js"));
        combinedScriptHtml();
    }
    ?>
    <?php
} else {
    noPerms();
}
