<?php

use UliCMS\Exceptions\FileNotFoundException;
use UliCMS\HTML\Script;
use UliCMS\Security\PermissionChecker;

class Template {

    public static function randomBanner() {
        $query = db_query("SELECT name, link_url, image_url, `type`, html FROM " . tbname("banner") . "
WHERE enabled = 1 and
(language IS NULL OR language='" . db_escape($_SESSION["language"]) . "') and
(
(date_from is not null and date_to is not null and CURRENT_DATE() >= date_from and CURRENT_DATE() <= date_to)
or
(date_from is not null and date_to is null and CURRENT_DATE() >= date_from )
or
(date_from is null and date_to is not null and CURRENT_DATE() <= date_to)
or
(date_from is null and date_to is null)
)
ORDER BY RAND() LIMIT 1") or die(Database::getError());
        if (db_num_rows($query) > 0) {
            while ($row = db_fetch_object($query)) {
                $type = "gif";
                if (isset($row->type)) {
                    if (!empty($row->type)) {
                        $type = $row->type;
                    }
                }
                if ($type == "gif") {
                    $title = Template::getEscape($row->name);
                    $link_url = Template::getEscape($row->link_url);
                    $image_url = Template::getEscape($row->image_url);
                    echo "<a href='$link_url' target='_blank'><img src='$image_url' title='$title' alt='$title' border=0></a>";
                } else if ($type == "html") {
                    echo $row->html;
                }
            }
        }
    }

    public static function outputContentElement() {
        $type = get_type();
        $output = "";
        switch ($type) {
            case "list":
                $output = Template::executeDefaultOrOwnTemplate("list");
                break;
            case "image":
                $output = Template::executeDefaultOrOwnTemplate("image");
                break;
            case "module":
                $page = get_page();

                if ($page["module"] != null and strlen($page['module']) > 0) {
                    no_cache();
                    $output = replaceShortcodesWithModules("[module=\"" . $page["module"] . "\"]");
                }
                break;
            case "video":
                $page = get_page();
                if ($page["video"] != null and strlen($page['video']) > 0) {
                    $output = replaceVideoTags("[video id=" . $page['video'] . "]");
                }
                break;
            case "audio":
                $page = get_page();
                if ($page["audio"] != null and strlen($page["audio"]) > 0) {
                    $output = replaceAudioTags("[audio id=" . $page['audio'] . "]");
                }
                break;
        }

        $output = apply_filter($output, "before_content");
        $output = apply_filter($output, "content");
        $output = apply_filter($output, "after_content");
        echo processHtmlBeforeOutput($output);
    }

    public static function getHomepageOwner() {
        $homepage_title = Settings::getLanguageSetting("homepage_owner", $_SESSION["language"]);
        return _esc($homepage_title);
    }

    public static function homepageOwner() {
        echo self::getHomepageOwner();
    }

    public static function footer() {
        enqueueScriptFile("lib/js/global.js");
        combinedScriptHtml();

        do_event("frontend_footer");
    }

    public static function executeModuleTemplate($module, $template) {
			
        $retval = "";
        $originalTemplatePath = getModulePath($module, true) . "templates/" . $template;
        $ownTemplatePath = getTemplateDirPath(get_theme(), true) . $module . "/" . $template;

        if (!endsWith($template, ".php")) {
            $originalTemplatePath .= ".php";
            $ownTemplatePath .= ".php";
        }
        ob_start();
        if (is_file($ownTemplatePath)) {
            require $ownTemplatePath;
        } else if (is_file($originalTemplatePath)) {
            require $originalTemplatePath;
        } else {
            $retval = ob_get_clean();
            throw new Exception("Template " . $module . "/" . $template . " not found!");
        }
        $retval = trim(ob_get_clean());
		
		return processHtmlBeforeOutput($retval);
    }
	

    public static function escape($value) {
        echo self::getEscape($value);
    }

    public static function getEscape($value) {
        return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
    }

    public static function logo() {
        if (Settings::get("logo_disabled") != "no") {
            return;
        }
        if (!Settings::get("logo_image")) {
            setconfig("logo_image", "");
        }
        if (!Settings::get("logo_disabled")) {
            setconfig("logo_disabled", "no");
        }

        $logo_storage_url = defined("ULICMS_DATA_STORAGE_URL") ? ULICMS_DATA_STORAGE_URL . "/content/images/" . Settings::get("logo_image") : "content/images/" . Settings::get("logo_image");
        $logo_storage_path = ULICMS_DATA_STORAGE_ROOT . "/content/images/" . Settings::get("logo_image");

        if (Settings::get("logo_disabled") == "no" and is_file($logo_storage_path)) {
            echo '<img class="website_logo" src="' . $logo_storage_url . '" alt="' . _esc(Settings::get("homepage_title")) . '"/>';
        }
    }

    // get current year
    public static function getYear($format = "Y") {
        return date($format);
    }

    public static function year($format = "Y") {
        echo self::getYear($format);
    }

    public static function getMotto() {
        // Existiert ein Motto für diese Sprache? z.B. motto_en
        $motto = Settings::get("motto_" . $_SESSION["language"]);

        // Ansonsten Standard Motto
        if (!$motto) {
            $motto = Settings::get("motto");
        }
        return _esc($motto);
    }

    public static function motto() {
        echo self::getMotto();
    }

    public static function executeDefaultOrOwnTemplate($template) {
        $retval = "";
        $originalTemplatePath = ULICMS_ROOT . "/default/" . $template;
        $ownTemplatePath = getTemplateDirPath(get_theme()) . "/" . $template;

        if (!endsWith($template, ".php")) {
            $originalTemplatePath .= ".php";
            $ownTemplatePath .= ".php";
        }

        ob_start();
        if (is_file($ownTemplatePath)) {
            require $ownTemplatePath;
        } else if (is_file($originalTemplatePath)) {
            require $originalTemplatePath;
        } else {
            $retval = ob_get_clean();
            throw new FileNotFoundException("Template " . $template . " not found!");
        }
        $retval = ob_get_clean();
        return processHtmlBeforeOutput($retval);
    }

    public static function headline($format = "<h1>%title%</h1>") {
        echo self::getHeadline($format);
    }

    public static function getHeadline($format = "<h1>%title%</h1>") {
        $retval = "";
        $id = get_ID();
        if (!$id) {
            $html = str_replace("%title%", get_title(null, true), $format);
            return $html;
        }
        $query = "SELECT show_headline FROM " . tbname("content") . " where id = $id";
        $query = Database::query($query);
        $result = Database::fetchObject($query);
        if ($result->show_headline) {
            $html = str_replace("%title%", get_title(null, true), $format);
        }
        return $html;
    }

    public static function doctype() {
        echo self::getDoctype();
    }

    public static function getDoctype() {
        $html = '<!doctype html>';
        $html .= "\r\n";
        return $html;
    }

    public static function ogHTMLPrefix() {
        echo self::getOgHTMLPrefix();
    }

    public static function getOgHTMLPrefix() {
        $language = getCurrentLanguage();
        $html = "<html prefix=\"og: http://ogp.me/ns#\" lang=\"$language\">\r\n";
        return $html;
    }

    public static function renderPartial($template, $theme = null) {
        if (!$theme) {
            $theme = get_theme();
        }

        $file = getTemplateDirPath($theme, true) . "partials/{$template}";
        $file = !endsWith($file, ".php") ? $file . ".php" : $file;
        if (!is_file($file)) {
            throw new FileNotFoundException("Partial Template {$template} of Theme {$theme} not found.");
        }
        ob_start();
        require $file;
        $result = trim(ob_get_clean());
        return $result;
    }

    public static function getHtml5Doctype() {
        return "<!doctype html>\r\n";
    }

    public static function html5Doctype() {
        echo self::getHtml5Doctype();
    }

    public static function getBaseMetas() {
        ob_start();
        self::baseMetas();
        return ob_get_clean();
    }

    public static function baseMetas() {
        $title_format = Settings::get("title_format");
        if ($title_format) {
            $title = $title_format;
            $title = str_ireplace("%homepage_title%", get_homepage_title(), $title);
            $title = str_ireplace("%title%", get_title(), $title);
            $title = str_ireplace("%motto%", get_motto(), $title);
            $title = apply_filter($title, "title_tag");
            echo "<title>" . $title . "</title>\r\n";
        }

        echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>';
        echo "\r\n";

        echo '<meta charset="utf-8"/>';
        echo "\r\n";

        if (!Settings::get("disable_no_format_detection")) {
            echo '<meta name="format-detection" content="telephone=no"/>';
            echo "\r\n";
        }

        $dir = dirname($_SERVER["SCRIPT_NAME"]);
        $dir = str_replace("\\", "/", $dir);

        if (endsWith($dir, "/") == false) {
            $dir .= "/";
        }

        $robots = Settings::get("robots");
        if ($robots) {
            $robots = apply_filter($robots, "meta_robots");
            echo '<meta name="robots" content="' . $robots . '"/>';
            echo "\r\n";
        }
        if (!Settings::get("hide_meta_generator")) {
            echo Template::executeDefaultOrOwnTemplate("powered-by");
            echo '<meta name="generator" content="UliCMS ' . cms_version() . '"/>';
            echo "\r\n";
        }
        output_favicon_code();
        echo "\r\n";

        if (!Settings::get("hide_shortlink") and ( is_200() or is_403())) {
            $shortlink = get_shortlink();
            if ($shortlink) {
                echo '<link rel="shortlink" href="' . $shortlink . '"/>';
                echo "\r\n";
            }
        }

        if (!Settings::get("hide_canonical") and ( is_200() or is_403())) {
            $canonical = get_canonical();
            if ($canonical) {
                echo '<link rel="canonical"  href="' . $canonical . '"/>';
                echo "\r\n";
            }
        }
        if (!Settings::get("no_autoembed_core_css")) {
            enqueueStylesheet("core.css");
            combinedStylesheetHtml();
            echo "\r\n";
        }

        $min_style_file = getTemplateDirPath(get_theme()) . "style.min.css";
        $min_style_file_realpath = getTemplateDirPath(get_theme(), true) . "style.min.css";
        $style_file = getTemplateDirPath(get_theme()) . "style.css";
        $style_file_realpath = getTemplateDirPath(get_theme(), true) . "style.css";
        if (is_file($style_file_realpath)) {
            $style_file .= "?time=" . File::getLastChanged($style_file_realpath);
            if (is_file($min_style_file_realpath)) {
                echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$min_style_file\"/>";
            } else if (is_file($style_file_realpath)) {
                echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$style_file\"/>";
            }
            echo "\r\n";
        }
        $keywords = get_meta_keywords();
        if (!$keywords) {
            $keywords = Settings::get("meta_keywords");
        }
        if ($keywords != "" && $keywords != false) {
            if (!Settings::get("hide_meta_keywords")) {
                $keywords = apply_filter($keywords, "meta_keywords");

                echo '<meta name="keywords" content="' . _esc($keywords) . '"/>';
                echo "\r\n";
            }
        }
        $description = get_meta_description();
        if (!$description) {
            $description = Settings::get("meta_description");
        }
        if ($description != "" && $description != false) {
            $description = apply_filter($description, "meta_description");
            $$description = _esc($description);
            if (!Settings::get("hide_meta_description")) {
                echo '<meta name="description" content="' . $description . '"/>';
                echo "\r\n";
            }
        }

        if (!Settings::get("disable_custom_layout_options")) {
            $font = Settings::get("default_font");
            if ($font == "google") {
                $google_font = Settings::get("google-font");
                if ($google_font) {
                    echo '<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=' . urlencode($google_font) . '"/>';
                    echo "\r\n";
                    $font = "'$google_font'";
                }
            }
            echo "
<style type=\"text/css\">
body{
font-family:" . $font . ";
font-size:" . Settings::get("font-size") . ";
background-color:" . Settings::get("body-background-color") . ";
color:" . Settings::get("body-text-color") . ";
}
</style>
";

            if (Settings::get("video_width_100_percent")) {
                echo "<style type=\"text/css\">
  video {
  width: 100% !important;
  height: auto !important;
  }
           </style>
        ";
            }
        }
        include_jquery();
        do_event("head");
    }

    public static function jQueryScript() {
        $jQueryurl = get_jquery_url();
        echo Script::FromFile($jQueryurl);
        do_event("after_jquery_include");
    }

    public static function getjQueryScript() {
        ob_start();
        self::jQueryScript();
        return ob_get_clean();
    }

    public static function content() {
        echo self::getContent();
    }

    public static function getContent() {
        $theme = get_theme();

        $errorPage403 = Settings::getLanguageSetting("error_page_403", getCurrentLanguage());
        $errorPage404 = Settings::getLanguageSetting("error_page_404", getCurrentLanguage());

        $content = null;
        if (is_200()) {
            $content = ContentFactory::getBySystemnameAndLanguage(get_requested_pagename(), getCurrentLanguage());

            if (!is_logged_in()) {
                db_query("UPDATE " . tbname("content") . " SET views = views + 1 WHERE systemname='" . Database::escapeValue($_GET["seite"]) . "' AND language='" . db_escape($_SESSION["language"]) . "'");
            }
        } else if (is_404()) {
            if ($errorPage404) {
                $content = ContentFactory::getByID($errorPage404);
            } else {
                return get_translation('PAGE_NOT_FOUND_CONTENT');
            }
        } else if (is_403()) {
            $theme = Settings::get("theme");
            if ($errorPage403) {
                $content = ContentFactory::getByID($errorPage404);
            } else {
                return get_translation('FORBIDDEN_COTENT');
            }
            return false;
        }
        if ($content->id === null) {
            return get_translation("no_content");
        }

        $htmlContent = $content->content;
        $htmlContent = apply_filter($htmlContent, "before_content");

        $htmlContent = apply_filter($htmlContent, "after_content");

        $data = CustomData::get();
        // it's possible to disable shortcodes for a page
        // define "disable_shortcodes in custom data / json
        if (is_false($data["disable_shortcodes"])) {
            $htmlContent = replaceShortcodesWithModules($htmlContent);
            $htmlContent = apply_filter($htmlContent, "content");
        }
        return $htmlContent;
    }

    public static function languageSelection() {
        $query = db_query("SELECT language_code, name FROM " . tbname("languages") . " ORDER by name");
        echo "<ul class='language_selection'>";
        while ($row = db_fetch_object($query)) {
            $domain = getDomainByLanguage($row->language_code);
            if ($domain) {
                echo "<li>" . "<a href='http://" . $domain . "'>" . $row->name . "</a></li>";
            } else {
                echo "<li>" . "<a href='./?language=" . $row->language_code . "'>" . $row->name . "</a></li>";
            }
        }
        echo "</ul>";
    }

    public static function getLanguageSelection() {
        ob_start();
        self::languageSelection();
        return ob_get_clean();
    }

    public static function getPoweredByUliCMS() {
        return get_translation("powered_by_ulicms");
    }

    // Gibt "Diese Seite läuft mit UliCMS" aus
    public static function poweredByUliCMS() {
        echo self::getPoweredByUliCMS();
    }

    public static function getComments() {
        return Template::executeModuleTemplate("core_comments", "comments.php");
    }

    public static function comments() {
        echo self::getComments();
    }

    public static function getEditButton() {
        $html = "";
        if (is_logged_in()) {
            $acl = new PermissionChecker(get_user_id());
            if ($acl->hasPermission("pages") and Flags::getNoCache() && is_200()) {
                $id = get_ID();
                $page = ContentFactory::getById($id);
                if (in_array($page->language, getAllLanguages(true))) {
                    $html .= '<div class="ulicms-edit">';
                    $html .= UliCMS\HTML\Link::ActionLink("pages_edit", get_translation("edit"), "page={$id}", array(
                                "class" => "btn btn-warning btn-edit"
                    ));
                    $html .= "</div>";
                }
            }
        }
        return $html;
    }

    public static function editButton() {
        echo self::getEditButton();
    }

    public static function getFooterText() {
        return replaceShortcodesWithModules(Settings::get("footer_text"));
    }

    public static function footerText() {
        echo self::getFooterText();
    }

}
