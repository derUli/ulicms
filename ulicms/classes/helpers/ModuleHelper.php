<?php

declare(strict_types=1);

use UliCMS\Models\Content\Language;
use UliCMS\HTML\Form;
use UliCMS\Constants\RequestMethod;

class ModuleHelper extends Helper {

    public static function buildAdminURL(string $module, ?string $suffix = null): string {
        $url = "?action=module_settings&module=" . $module;
        if ($suffix !== null and ! empty($suffix)) {
            $url .= "&" . $suffix;
        }
        $url = rtrim($url, "&");
        return $url;
    }

    public static function buildModuleRessourcePath(string $module, string $path, bool $absolute = false): string {
        $path = trim($path, "/");
        return getModulePath($module, $absolute) . $path;
    }

    public static function buildRessourcePath(string $module, string $path): string {
        return self::buildModuleRessourcePath($module, $path);
    }

    // TODO: Refactor this method
    public static function getFirstPageWithModule(?string $module = null, ?string $language = null): ?object {
        if (is_null($language)) {
            $language = getCurrentLanguage();
        }
        $args = array(
            1,
            $language
        );
        $sql = "select * from {prefix}content where active = ? and language = ?";
        $result = Database::pQuery($sql, $args, true);
        while ($dataset = Database::fetchObject($result)) {
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

    public static function buildActionURL(string $action, ?string $suffix = null, bool $prependSuffixIfRequired = false): string {
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

    public static function getAllEmbedModules(): array {
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

            if (!file_exists($noembedfile1) and ! file_exists($noembedfile2) and $embed_attrib) {
                $retval[] = $module;
            }
        }
        return $retval;
    }

    public static function getMainController(string $module): ?Controller {
        $controller = null;
        $main_class = getModuleMeta($module, "main_class");
        if ($main_class) {
            $controller = ControllerRegistry::get($main_class);
        }
        return $controller;
    }

    public static function getMainClass(string $module): ?Controller {
        return self::getMainController($module);
    }

    public static function isEmbedModule(string $module): bool {
        $retval = true;
        $noembedfile1 = Path::Resolve("ULICMS_DATA_STORAGE_ROOT/content/modules/$module/.noembed");
        $noembedfile2 = Path::Resolve("ULICMS_DATA_STORAGE_ROOT/content/modules/$module/noembed.txt");

        $embed_attrib = true;

        $meta_attr = getModuleMeta($module, "embed");
        if (is_bool($meta_attr)) {
            $embed_attrib = $meta_attr;
        }

        if (file_exists($noembedfile1) or file_exists($noembedfile2) or ! $embed_attrib) {
            $retval = false;
        }
        return $retval;
    }

    public static function getBaseUrl(string $suffix = "/"): string {
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

    public static function getFullPageURLByID(?int $page_id = null, ?string $suffix = null): string {
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
    public static function underscoreToCamel(string $str): string {
        // Remove underscores, capitalize words, squash, lowercase first.
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
    }

    public static function buildMethodCall(string $sClass, string $sMethod, ?string $suffix = null): string {
        $result = "sClass=" . urlencode($sClass) . "&sMethod=" . urlencode($sMethod);
        if (StringHelper::isNotNullOrWhitespace($suffix)) {
            $result .= "&" . trim($suffix);
        }
        return $result;
    }

    public static function buildMethodCallUrl(string $sClass, string $sMethod, ?string $suffix = null): string {
        return "index.php?" . self::buildMethodCall($sClass, $sMethod, $suffix);
    }

    public static function buildMethodCallUploadForm(string $sClass, string $sMethod, array $otherVars = [], string $requestMethod = RequestMethod::POST, array $htmlAttributes = []): string {
        $htmlAttributes["enctype"] = "multipart/form-data";
        return self::buildMethodCallForm($sClass, $sMethod, $otherVars, $requestMethod, $htmlAttributes);
    }

    public static function buildMethodCallForm(string $sClass, string $sMethod, array $otherVars = [], string $requestMethod = RequestMethod::POST, array $htmlAttributes = []): string {
        return Form::buildMethodCallForm($sClass, $sMethod, $otherVars, $requestMethod, $htmlAttributes);
    }

    public static function buildMethodCallButton(string $sClass, string $sMethod, string $buttonText, array $buttonAttributes = ["class" => "btn btn-default", "type" => "submit"], array $otherVars = [], array $formAttributes = [], string $requestMethod = RequestMethod::POST): string {
        return Form::buildMethodCallButton($sClass, $sMethod, $buttonText, $buttonAttributes, $otherVars, $formAttributes, $requestMethod);
    }

    public static function deleteButton(string $url, array $otherVars = [], array $htmlAttributes = []): string {
        return Form::deleteButton($url, $otherVars, $htmlAttributes);
    }

    public static function buildHTMLAttributesFromArray(array $attributes = []): string {
        $html = "";
        foreach ($attributes as $key => $value) {
            $val = is_bool($value) ? strbool($value) : $value;
            $html .= Template::getEscape($key) . '="' . Template::getEscape($val) . '" ';
        }
        $html = trim($html);
        return $html;
    }

    public static function buildQueryString($data, bool $forHtml = true): string {
        $seperator = $forHtml ? "&amp;" : "&";
        return http_build_query($data, '', $seperator);
    }

    public static function endForm(): string {
        return Form::endForm();
    }

}
