<?php

use UliCMS\Models\Content\Comment;
use UliCMS\HTML\Script;
use UliCMS\Helpers\DataTablesHelper;

$admin_logo = Settings::get("admin_logo");
if (!$admin_logo) {
    $admin_logo = "gfx/logo.png";
}

// translation for select2 dropdown boxes
$select2TranslationFile = "../node_modules/select2/dist/js/i18n/" . getSystemLanguage() . ".js";
$select2Language = getSystemLanguage();
if (!file_exists($select2TranslationFile)) {
    $select2TranslationFile = "../node_modules/select2/dist/js/i18n/en.js";
    $select2Language = "en";
}

$permissionChecker = new UliCMS\Security\PermissionChecker(get_user_id());
?>
<!DOCTYPE html>
<html data-select2-language="<?php esc($select2Language) ?>">
    <head>
        <meta name="viewport"
              content="width=device-width, user-scalable=yes, initial-scale=1" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="google" content="notranslate" />
        <title>[<?php Template::escape(Settings::get("homepage_title")); ?>] - UliCMS</title>
        <script src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
        <?php
        $styles = [];
        ?>
        <?php
        $scripts = array(
            "../node_modules/jquery/dist/jquery.min.js",
            "../node_modules/js-url/url.min.js",
            "../node_modules/jquery-datetimepicker/build/jquery.datetimepicker.full.js",
            "../node_modules/jquery-form/dist/jquery.form.min.js",
            "scripts/vallenato/vallenato.js",
            "scripts/util.js",
            "scripts/global.js",
            "../node_modules/bootstrap/dist/js/bootstrap.min.js",
            "../node_modules/bootstrap-toggle/js/bootstrap-toggle.min.js",
            "../node_modules/select2/dist/js/select2.min.js",
            "../node_modules/bootbox/bootbox.min.js",
            $select2TranslationFile,
            "../node_modules/datatables/media/js/jquery.dataTables.min.js",
            "../lib/js/global.js",
        );
        ?>
        <?php
        if (is_logged_in()) {
            $scripts[] = "../node_modules/jscolor-picker/jscolor.min.js";
        }
        foreach ($scripts as $script) {
            enqueueScriptFile($script);
        }
        if (is_logged_in()) {
            echo Script::fromFile("ckeditor/ckeditor.js");
        }
        combinedScriptHtml();
        ?>
        <?php require "inc/touch_icons.php"; ?>
        <link rel="stylesheet" type="text/css"
              href="scripts/vallenato/vallenato.css" />
        <link rel="stylesheet" type="text/css"
              href="../node_modules/datatables/media/css/jquery.dataTables.min.css" />
        <link rel="stylesheet"
              href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
              <?php
              $styles[] = "../node_modules/bootstrap/dist/css/bootstrap.min.css";
              $styles[] = "../node_modules/codemirror-minified/lib/codemirror.css";
              $styles[] = "css/modern.scss";
              $styles[] = "../node_modules/bootstrap-toggle/css/bootstrap-toggle.min.css";
              $styles[] = "../node_modules/select2/dist/css/select2.min.css";
              $styles[] = "../node_modules/jquery-datetimepicker/build/jquery.datetimepicker.min.css";

              foreach ($styles as $style) {
                  enqueueStylesheet($style);
              }

              echo UliCMS\HTML\Style::fromExternalFile("../node_modules/password-strength-meter/dist/password.min.css");
              combinedStylesheetHtml();
              ?>
              <?php
              do_event("admin_head");
              ?>
    </head>
    <?php
    do_event("before_backend_header");
    ?>
    <?php
    $cssClasses = "";
    if (get_user_id()) {
        $cssClasses .= "user-" . get_user_id() . "-logged-in ";
    } else {
        $cssClasses .= "not-logged-in ";
    }
    if (get_action()) {
        $cssClasses .= "action-" . get_action();
    } else {
        $cssClasses .= "no-action";
    }
    ?>
    <body class="<?php esc($cssClasses); ?>"
          data-datatables-translation="<?php echo DataTablesHelper::getLanguageFileURL(getSystemLanguage()); ?>"
          data-ckeditor-skin="<?php esc(Settings::get("ckeditor_skin")); ?>">
              <?php
              do_event("after_backend_header");
              ?>
        <div
            class="container main <?php
            if (get_action()) {
                echo 'action-' . Template::getEscape(get_action());
            }
            ?>">
            <div class="row">
                <div class="col-xs-7">
                    <a href="../" title="<?php translate("goto_frontend"); ?>"><img
                            src="<?php Template::escape($admin_logo); ?>" alt="UliCMS"
                            class="ulicms-logo"></a>
                </div>
                <div class="col-xs-5 menu-container">
                    <?php
                    if (is_logged_in()) {
                        $colClass = $permissionChecker->hasPermission("comments_manage") ? "col-xs-4" : "col-xs-6";
                        ?>
                        <div class="row pull-right top-right-icons">
                            <div class="<?php esc($colClass); ?>">
                                <a href="#" id="menu-clear-cache"
                                   data-url="<?php echo ModuleHelper::buildMethodCallUrl("PerformanceSettingsController", "clearCache", "clear_cache=1"); ?>">
                                    <i class="fas fa-broom"></i></a>
                                <a href="#" id="menu-clear-cache-loading" style="display: none;"><i class="fa fa-spinner fa-spin"></i></a>
                            </div>
                            <?php
                            if ($permissionChecker->hasPermission("comments_manage")) {
                                $count = Comment::getUnreadCount();
                                ?>
                                <div class="<?php esc($colClass); ?>">
                                    <div class="comment-counter">
                                        <a href="<?php echo ModuleHelper::buildActionURL("comments_manage"); ?>"><i class="fa fa-comments"></i>
                                            <?php
                                            if ($count) {
                                                ?>
                                                <div class="count" data-count="<?php echo $count ?>">
                                                    <?php echo $count; ?>
                                                </div>
                                            <?php } ?></a>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="<?php esc($colClass); ?>">
                                <a id="menu-toggle"><i class="fa fa-bars"></i> </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="main-content">
                <?php
                if (is_logged_in() and version_compare(phpversion(), '7.1', '<')) {
                    require_once "inc/php_upgrade.php";
                }

