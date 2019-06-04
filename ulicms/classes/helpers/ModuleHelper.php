<?php

use UliCMS\Models\Content\Language;
use UliCMS\HTML\Form;
use UliCMS\Constants\RequestMethod;

class ModuleHelper extends Helper {

    public static function buildAdminURL($module, $suffix = null) {
        $url = "?action=module_settings&module=" . $module;
        if ($suffix !== null and ! empty($suffix)) {
            $url .= "&" . $suffix;
        }
        $url = rtrim($url, "&");
        return $url;
    }

    public static function buildModuleRessourcePath($module, $path, $absolute = false) {
        $path = trim($path, "/");
        return getModulePath($module, $absolute) . $path;
    }

    public static function buildRessourcePath($module, $path) {
        return self::buildModuleRessourcePath($module, $path);
    }

    public static function getFirstPageWithModule($module = null, $language = null) {
        if (is_null($language)) {
            $language = getCurrentLanguage();
        }
        $args = array(
            1,
            $language
        );
        $sql = "select * from {prefix}content where active = ? and language = ?";
        $query = Database::pQuery($sql, $args, true);
        while ($dataset = Database::fetchObject($query)) {
            $content = $dataset->content;
            $content = str_replace("&quot;", "\"", $content);
            if (!is_null($dataset->module) and ! empty($dataset->module) and $dataset->type == "module") {
                if (!$module or ( $module and $dataset->module == $module)) {
                    return $dataset;
                }
            } else if ($module) {
                if (preg_match("/\[module=\"" . preg_quote($module) . "\"\]/", $content)) {
                    return $dataset;
                }
            } else {
                if (preg_match("/\[module=\".+\"\]/", $content)) {
                    return $dataset;
                }
            }
        }
        return null;
    }

    public static function buildActionURL($action, $suffix = null, $prependSuffixIfRequired = false) {
        $url = "?action=" . $action;
        if ($suffix !== null and ! empty($suffix)) {
            $url .= "&" . $suffix;
        }
        if (!is_admin_dir() and $prependSuffixIfRequired) {
            $url = "admin/" . $url;
        }
        $url = rtrim($url, "&");
        return $url;
    }

    public static function getAllEmbedModules() {
        $retval = [];
        $modules = getAllModules();
        foreach ($modules as $module) {
            $noembedfile1 = Path::Resolve("ULICMS_DATA_STORAGE_ROOT/content/modules/$module/.noembed");
            $noembedfile2 = Path::Resolve("ULICMS_DATA_STORAGE_ROOT/content/modules/$module/noembed.txt");

            $embed_attrib = true;

            $meta_attr = getModuleMeta($module, "embed");
            if (is_bool($meta_attr)) {
                $embed_attrib = $meta_attr;
            }

            if (!is_file($noembedfile1) and ! is_file($noembedfile2) and $embed_attrib) {
                $retval[] = $module;
            }
        }
        return $retval;
    }

    public static function getMainController($module) {
        $controller = null;
        $main_class = getModuleMeta($module, "main_class");
        if ($main_class) {
            $controller = ControllerRegistry::get($main_class);
        }
        return $controller;
    }

    public static function isEmbedModule($module) {
        $retval = true;
        $noembedfile1 = Path::Resolve("ULICMS_DATA_STORAGE_ROOT/content/modules/$module/.noembed");
        $noembedfile2 = Path::Resolve("ULICMS_DATA_STORAGE_ROOT/content/modules/$module/noembed.txt");

        $embed_attrib = true;

        $meta_attr = getModuleMeta($module, "embed");
        if (is_bool($meta_attr)) {
            $embed_attrib = $meta_attr;
        }

        if (is_file($noembedfile1) or is_file($noembedfile2) or ! $embed_attrib) {
            $retval = false;
        }
        return $retval;
    }

    public static function getBaseUrl($suffix = "/") {
        $domain = get_http_host();

        $dirname = dirname(get_request_uri());

        // Replace backslashes with slashes (Windows)
        $dirname = str_replace("\\", "/", $dirname);

        if (is_admin_dir()) {
            $dirname = dirname(dirname($dirname . "/.."));
        }

        // Replace backslashes with slashes (Windows)
        $dirname = str_replace("\\", "/", $dirname);

        $dirname = rtrim($dirname, "/");

        return get_site_protocol() . $domain . $dirname . $suffix;
    }

    public static function getFullPageURLByID($page_id = null, $suffix = null) {
        if (!$page_id) {
            $page_id = get_id();
        }
        if (!$page_id) {
            return null;
        }
        $page = ContentFactory::getByID($page_id);
        if (is_null($page->id)) {
            return null;
        }
        if ($page instanceof Language_Link) {
            $language = new Language($page->link_to_language);
            if (!is_null($language->getID()) and StringHelper::isNotNullOrWhitespace($language->getLanguageLink()))
                return $language->getLanguageLink();
        }
        $domain = getDomainByLanguage($page->language);
        $dirname = dirname(get_request_uri());

        $dirname = str_replace("\\", "/", $dirname);

        if (is_admin_dir()) {
            $dirname = dirname(dirname($dirname . "/.."));
        }
        if (!startsWith($dirname, "/")) {
            $dirname = "/" . $dirname;
        }
        if (!endsWith($dirname, "/")) {
            $dirname = $dirname . "/";
        }

        // Replace backslashes with slashes (Windows)
        $dirname = str_replace("\\", "/", $dirname);

        $currentLanguage = isset($_SESSION["language"]) ? $_SESSION["language"] : Settings::get("default_language");
        if ($domain) {
            $url = get_site_protocol() . $domain . $dirname . $page->slug . ".html";
            if (!is_null($suffix)) {
                $url .= "?{$suffix}";
            }
        } else {
            if ($page->language != $currentLanguage) {
                $url = get_protocol_and_domain() . $dirname . $page->slug . ".html" . "?language=" . $page->language;
                if (!is_null($suffix)) {
                    $url .= "&{$suffix}";
                }
            } else {
                $url = get_protocol_and_domain() . $dirname . $page->slug . ".html";
                if (!is_null($suffix)) {
                    $url .= "?{$suffix}";
                }
            }
        }
        return $url;
    }

    /**
     * Convert underscore_strings to camelCase.
     *
     * @param {string} $str
     */
    public static function underscoreToCamel($str) {
        // Remove underscores, capitalize words, squash, lowercase first.
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
    }

    public static function buildMethodCall($sClass, $sMethod, $suffix = null) {
        $result = "sClass=" . urlencode($sClass) . "&sMethod=" . urlencode($sMethod);
        if (StringHelper::isNotNullOrWhitespace($suffix)) {
            $result .= "&" . trim($suffix);
        }
        return $result;
    }

    public static function buildMethodCallUrl($sClass, $sMethod, $suffix = null) {
        return "index.php?" . self::buildMethodCall($sClass, $sMethod, $suffix);
    }

    public static function buildMethodCallUploadForm($sClass, $sMethod, $otherVars = [], $requestMethod = RequestMethod::POST, $htmlAttributes = []) {
        $htmlAttributes["enctype"] = "multipart/form-data";
        return self::buildMethodCallForm($sClass, $sMethod, $otherVars, $requestMethod, $htmlAttributes);
    }

    public static function buildMethodCallForm($sClass, $sMethod, $otherVars = [], $requestMethod = RequestMethod::POST, $htmlAttributes = []) {
        return Form::buildMethodCallForm($sClass, $sMethod, $otherVars, $requestMethod, $htmlAttributes);
    }

    public static function buildMethodCallButton($sClass, $sMethod, $buttonText, $buttonAttributes = array("class" => "btn btn-default", "type" => "submit"), $otherVars = [], $formAttributes = [], $requestMethod = RequestMethod::POST) {
        return Form::buildMethodCallButton($sClass, $sMethod, $buttonText, $buttonAttributes, $otherVars, $formAttributes, $requestMethod);
    }

    public static function deleteButton($url, $otherVars = [], $htmlAttributes = []) {
        return Form::deleteButton($url, $otherVars, $htmlAttributes);
    }

    public static function buildHTMLAttributesFromArray($attributes = []) {
        $html = "";
        foreach ($attributes as $key => $value) {
            $val = is_bool($value) ? strbool($value) : $value;
            $html .= Template::getEscape($key) . '="' . Template::getEscape($val) . '" ';
        }
        $html = trim($html);
        return $html;
    }

    public static function buildQueryString($data, $forHtml = true) {
        $seperator = $forHtml ? "&amp;" : "&";
        return http_build_query($data, '', $seperator);
    }

    public static function endForm() {
        return Form::endForm();
    }

}
