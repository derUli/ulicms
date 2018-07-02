<?php

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
        echo apply_filter('<script type="text/javascript" src="lib/js/global.js"></script>', "global_js_script_tag");
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

    public static function getYear()
    {
        return date("Y");
    }

    public static function year()
    {
        echo self::getYear();
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
        $html = '<html prefix="og: http://ogp.me/ns#" lang="' . getCurrentLanguage() . '">';
        $html .= "\r\n";
        return $html;
    }
    // @TODO Restliche Funktionen aus templating.php implementieren
}
