<?php
use UliCMS\Exceptions\FileNotFoundException;
use UliCMS\HTML\Script;

class Template
{

    public static function randomBanner()
    {
        $query = db_query("SELECT name, link_url, image_url, `type`, html FROM " . tbname("banner") . " WHERE language IS NULL OR language='" . db_escape($_SESSION["language"]) . "'ORDER BY RAND() LIMIT 1");
        if (db_num_rows($query) > 0) {
            while ($row = db_fetch_object($query)) {
                $type = "gif";
                if (isset($row->type)) {
                    if (! empty($row->type)) {
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

    public static function outputContentElement()
    {
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
        $output = apply_filter($output, "content");
        echo $output;
    }

    public static function poweredByUliCMS()
    {
        translation("POWERED_BY_ULICMS");
    }

    public static function getHomepageOwner()
    {
        $homepage_title = Settings::getLang("homepage_title", $_SESSION["language"]);
        return htmlspecialchars($homepage_title, ENT_QUOTES, "UTF-8");
    }

    public static function homepageOwner()
    {
        echo self::getHomepageOwner();
    }

    public static function footer()
    {
        enqueueScriptFile("lib/js/global.js");
        combinedScriptHtml();
        
        do_event("frontend_footer");
    }

    public static function executeModuleTemplate($module, $template)
    {
        $retval = "";
        $originalTemplatePath = getModulePath($module, true) . "templates/" . $template;
        $ownTemplatePath = getTemplateDirPath(get_theme(), true) . $module . "/" . $template;
        
        if (! endsWith($template, ".php")) {
            $originalTemplatePath .= ".php";
            $ownTemplatePath .= ".php";
        }
        ob_start();
        if (is_file($ownTemplatePath)) {
            include $ownTemplatePath;
        } else if (is_file($originalTemplatePath)) {
            include $originalTemplatePath;
        } else {
            $retval = ob_get_clean();
            throw new Exception("Template " . $module . "/" . $template . " not found!");
        }
        $retval = ob_get_clean();
        return $retval;
    }

    public static function escape($value)
    {
        echo htmlspecialchars($value, ENT_QUOTES, "UTF-8");
    }

    public static function getEscape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
    }

    public static function logo()
    {
        if (Settings::get("logo_disabled") != "no") {
            return;
        }
        if (! Settings::get("logo_image")) {
            setconfig("logo_image", "");
        }
        if (! Settings::get("logo_disabled")) {
            setconfig("logo_disabled", "no");
        }
        
        $logo_storage_url = defined("ULICMS_DATA_STORAGE_URL") ? ULICMS_DATA_STORAGE_URL . "/content/images/" . Settings::get("logo_image") : "content/images/" . Settings::get("logo_image");
        $logo_storage_path = ULICMS_DATA_STORAGE_ROOT . "/content/images/" . Settings::get("logo_image");
        
        if (Settings::get("logo_disabled") == "no" and is_file($logo_storage_path)) {
            echo '<img class="website_logo" src="' . $logo_storage_url . '" alt="' . htmlspecialchars(Settings::get("homepage_title"), ENT_QUOTES, "UTF-8") . '"/>';
        }
    }

    // get current year
    public static function getYear($format = "Y")
    {
        return date($format);
    }

    public static function year($format = "Y")
    {
        echo self::getYear($format);
    }

    public static function getMotto()
    {
        // Existiert ein Motto für diese Sprache? z.B. motto_en
        $motto = Settings::get("motto_" . $_SESSION["language"]);
        
        // Ansonsten Standard Motto
        if (! $motto) {
            $motto = Settings::get("motto");
        }
        return htmlspecialchars($motto, ENT_QUOTES, "UTF-8");
    }

    public static function motto()
    {
        echo self::getMotto();
    }

    public static function executeDefaultOrOwnTemplate($template)
    {
        $retval = "";
        $originalTemplatePath = ULICMS_ROOT . "/default/" . $template;
        $ownTemplatePath = getTemplateDirPath(get_theme()) . "/" . $template;
        
        if (! endsWith($template, ".php")) {
            $originalTemplatePath .= ".php";
            $ownTemplatePath .= ".php";
        }
        
        ob_start();
        if (is_file($ownTemplatePath)) {
            include $ownTemplatePath;
        } else if (is_file($originalTemplatePath)) {
            include $originalTemplatePath;
        } else {
            $retval = ob_get_clean();
            throw new Exception("Template " . $template . " not found!");
        }
        $retval = ob_get_clean();
        return $retval;
    }

    public static function headline($format = "<h1>%title%</h1>")
    {
        echo self::getHeadline($format);
    }

    public static function getHeadline($format = "<h1>%title%</h1>")
    {
        $retval = "";
        $id = get_ID();
        if (! $id) {
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

    public static function doctype()
    {
        echo self::getDoctype();
    }

    public static function getDoctype()
    {
        $html = '<!doctype html>';
        $html .= "\r\n";
        return $html;
    }

    public static function ogHTMLPrefix()
    {
        echo self::getOgHTMLPrefix();
    }

    public static function getOgHTMLPrefix()
    {
        $language = getCurrentLanguage();
        $html = "<html prefix=\"og: http://ogp.me/ns#\" lang=\"$language\">\r\n";
        return $html;
    }

    public static function renderPartial($template, $theme = null)
    {
        if (! $theme) {
            $theme = get_theme();
        }
        
        $file = getTemplateDirPath($theme, true) . "partials/{$template}";
        $file = ! endsWith($file, ".php") ? $file . ".php" : $file;
        if (! is_file($file)) {
            throw new FileNotFoundException("Partial Template {$template} of Theme {$theme} not found.");
        }
        ob_start();
        include $file;
        $result = trim(ob_get_clean());
        return $result;
    }

    public static function getHtml5Doctype()
    {
        return "<!doctype html>\r\n";
    }

    public static function html5Doctype()
    {
        echo self::getHtml5Doctype();
    }

    public static function getBaseMetas()
    {
        ob_start();
        self::baseMetas();
        return ob_get_clean();
    }

    public static function baseMetas()
    {
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
        
        if (! Settings::get("disable_no_format_detection")) {
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
        if (! Settings::get("hide_meta_generator")) {
            echo Template::executeDefaultOrOwnTemplate("powered-by");
            echo '<meta name="generator" content="UliCMS ' . cms_version() . '"/>';
            echo "\r\n";
        }
        output_favicon_code();
        echo "\r\n";
        
        if (! Settings::get("hide_shortlink") and (is_200() or is_403())) {
            $shortlink = get_shortlink();
            if ($shortlink) {
                echo '<link rel="shortlink" href="' . $shortlink . '"/>';
                echo "\r\n";
            }
        }
        
        if (! Settings::get("hide_canonical") and (is_200() or is_403())) {
            $canonical = get_canonical();
            if ($canonical) {
                echo '<link rel="canonical"  href="' . $canonical . '"/>';
                echo "\r\n";
            }
        }
        if (! Settings::get("no_autoembed_core_css")) {
            enqueueStylesheet("core.css");
            combinedStylesheetHtml();
            echo "\r\n";
        }
        
        $min_style_file = getTemplateDirPath(get_theme()) . "style.min.css";
        $min_style_file_realpath = getTemplateDirPath(get_theme(), true) . "style.min.css";
        $style_file = getTemplateDirPath(get_theme()) . "style.css";
        $style_file_realpath = getTemplateDirPath(get_theme(), true) . "style.css";
        $style_file .= "?time=" . File::getLastChanged($style_file_realpath);
        if (is_file($min_style_file_realpath)) {
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$min_style_file\"/>";
        } else if (is_file($style_file_realpath)) {
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$style_file\"/>";
        }
        echo "\r\n";
        $keywords = get_meta_keywords();
        if (! $keywords) {
            $keywords = Settings::get("meta_keywords");
        }
        if ($keywords != "" && $keywords != false) {
            if (! Settings::get("hide_meta_keywords")) {
                $keywords = apply_filter($keywords, "meta_keywords");
                $keywords = htmlentities($keywords, ENT_QUOTES, "UTF-8");
                echo '<meta name="keywords" content="' . $keywords . '"/>';
                echo "\r\n";
            }
        }
        $description = get_meta_description();
        if (! $description) {
            $description = Settings::get("meta_description");
        }
        if ($description != "" && $description != false) {
            $description = apply_filter($description, "meta_description");
            $$description = htmlentities($description, ENT_QUOTES, "UTF-8");
            if (! Settings::get("hide_meta_description")) {
                echo '<meta name="description" content="' . $description . '"/>';
                echo "\r\n";
            }
        }
        
        if (! Settings::get("disable_custom_layout_options")) {
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

    public static function jQueryScript()
    {
        $jQueryurl = get_jquery_url();
        echo Script::FromFile($jQueryurl);
        do_event("after_jquery_include");
    }

    public static function getjQueryScript()
    {
        ob_start();
        self::jQueryScript();
        return ob_get_clean();
    }
    public static function content()
    {
        $status = check_status();
        if ($status == '404 Not Found') {
            if (is_file(getTemplateDirPath($theme) . "404.php")) {
                $theme = Settings::get("theme");
                include getTemplateDirPath($theme) . "404.php";
            } else {
                translate('PAGE_NOT_FOUND_CONTENT');
            }
            return false;
        } else if ($status == '403 Forbidden') {
            
            $theme = Settings::get("theme");
            if (is_file(getTemplateDirPath($theme) . '403.php')) {
                include getTemplateDirPath($theme) . '403.php';
            } else {
                translate('FORBIDDEN_COTENT');
            }
            return false;
        }
        
        if (! is_logged_in()) {
            db_query("UPDATE " . tbname("content") . " SET views = views + 1 WHERE systemname='" . Database::escapeValue($_GET["seite"]) . "' AND language='" . db_escape($_SESSION["language"]) . "'");
        }
        return import($_GET["seite"]);
    }
    
    public static function getContent()
    {
        ob_start();
        self::content();
        return ob_get_clean();
    }
    // TODO Restliche Funktionen aus templating.php implementieren
}
