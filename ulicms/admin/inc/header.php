<?php
$admin_logo = Settings::get("admin_logo");
if (!$admin_logo) {
    $admin_logo = "gfx/logo.png";
}

// translation for select2 dropdown boxes
$select2TranslationFile = "scripts/js/i18n/" . getSystemLanguage() . ".js";
$select2Language = getSystemLanguage();
if (!file_exists($select2TranslationFile)) {
    $select2TranslationFile = "scripts/js/i18n/en.js";
    $select2Language = "en";
}
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
        $styles = array();
        ?>
        <?php
        $enq = array(
            "scripts/php.js/strip_tags.js",
            "scripts/php.js/htmlspecialchars.js",
            "../node_modules/jquery/dist/jquery.min.js",
            "scripts/datetimepicker/jquery.datetimepicker.full.js",
            "scripts/jquery.form.min.js",
            "scripts/vallenato/vallenato.js",
            "../node_modules/codemirror-minified/lib/codemirror.js",
            "../node_modules/codemirror-minified/mode/php/php.js",
            "../node_modules/codemirror-minified/mode/xml/xml.js",
            "../node_modules/codemirror-minified/mode/javascript/javascript.js",
            "../node_modules/codemirror-minified/mode/clike/clike.js",
            "../node_modules/codemirror-minified/mode/css/css.js",
            "scripts/url.min.js",
            "scripts/util.js",
            "scripts/users.js",
            "scripts/global.js",
            "scripts/bootstrap.min.js",
            "scripts/js/select2.min.js",
            "../node_modules/bootbox/bootbox.min.js",
            $select2TranslationFile,
            "scripts/datatables/datatables.min.js",
            "../lib/js/global.js"
        );

        if (is_logged_in()) {
            $enq[] = "scripts/cookie.js";
            $enq[] = "scripts/jquery-shiftclick.js";
            $enq[] = "scripts/shift_checkbox.js";
        }
        if (!is_mobile()) {
            $enq[] = "scripts/doubletaptogo/doubletaptogo.min.js";
        }
        ?>
        <?php
        foreach ($enq as $script) {
            enqueueScriptFile($script);
        }
        ?>
        <script type="text/javascript" src="ckeditor/ckeditor.js"></script>

        <?php combinedScriptHtml(); ?>
        <script type="text/javascript" src="scripts/jscolor/jscolor.min.js"></script>
        <link rel="stylesheet" type="text/css"
              href="scripts/vallenato/vallenato.css" />

        <?php include "inc/touch_icons.php"; ?>
        <?php
        $styles[] = "css/bootstrap.min.css";
        $styles[] = "../node_modules/codemirror-minified/lib/codemirror.css";
        $styles[] = "css/modern.scss";
        $styles[] = "scripts/css/select2.min.css";
        $styles[] = "scripts/datetimepicker/jquery.datetimepicker.min.css";

        foreach ($styles as $style) {
            enqueueStylesheet($style);
        }

        combinedStylesheetHtml();
        ?>
        <?php
        do_event("admin_head");
        ?>

        <link rel="stylesheet" type="text/css"
              href="scripts/datatables/datatables.min.css" />
        <link rel="stylesheet"
              href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" />
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
          data-datatables-translation="<?php echo DataTablesHelper::getLanguageFileURL(getSystemLanguage()); ?>">
              <?php
              do_event("after_backend_header");
              ?>
        <div
            class="container-fluid main <?php
            if (get_action()) {
                echo 'action-' . Template::getEscape(get_action());
            }
            ?>">

            <div class="row">
                <div class="col-xs-8">
                    <a href="../" title="<?php translate("goto_frontend"); ?>"><img
                            src="<?php Template::escape($admin_logo); ?>" alt="UliCMS"
                            class="ulicms-logo"></a>
                </div>
                <div class="col-xs-4 menu-container">
                    <?php
                    if (is_logged_in()) {
                        ?>
                        <div class="row pull-right top-right-icons">
                            <div class="col-xs-6">
                                <a href="#" id="menu-clear-cache"
                                   data-url="<?php echo ModuleHelper::buildMethodCallUrl("PerformanceSettingsController", "clearCache", "clear_cache=1"); ?>">
                                    <i class="fas fa-broom"></i>
                                </a> <a href="#" id="menu-clear-cache-loading"
                                        style="display: none;"><i class="fa fa-spinner fa-spin"></i></a>
                            </div>
                            <div class="col-xs-6">
                                <a id="menu-toggle"><i class="fa fa-bars"></i> </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row main-content">
                <div class="col-xs-12">