<?php

declare(strict_types=1);

use App\Utils\File;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\DatasetNotFoundException;
use App\HTML\Script;
use App\Security\PermissionChecker;
use MatthiasMullie\Minify;
use App\Models\Content\Advertisement\Banners;

class Template
{
    public static function getBodyClasses(): string
    {
        $str = get_ID() ? "page-id-" . get_ID() . " " : "";

        $str .= (is_frontpage() ? "home " : "");
        $str .= (is_404() ? "error404 " : "");
        $str .= (is_403() ? "error403 " : "");
        $str .= ((is_404() or is_403()) ? "errorPage " : "page ");
        $str .= (is_mobile() ? "mobile " : "desktop ");
        $str .= (containsModule(get_slug()) ?
                " containsModule " : "");

        $str = trim($str);
        $str = apply_filter($str, "body_classes");
        return $str;
    }

    public static function bodyClasses()
    {
        echo self::getBodyClasses();
    }

    public static function randomBanner(): void
    {
        $banner = Banners::getRandom();
        if ($banner) {
            echo $banner->render();
        }
    }

    public static function outputContentElement(): void
    {
        $type = get_type();
        $output = '';
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
                    Vars::setNoCache(true);
                    $output = replaceShortcodesWithModules(
                        "[module=\"" . $page["module"] . "\"]"
                    );
                }
                break;
            case "video":
                $page = get_page();
                if ($page["video"] != null and strlen($page['video']) > 0) {
                    $output = replaceVideoTags(
                        "[video id=" . $page['video'] . "]"
                    );
                }
                break;
            case "audio":
                $page = get_page();
                if ($page["audio"] != null and strlen($page["audio"]) > 0) {
                    $output = replaceAudioTags(
                        "[audio id=" . $page['audio'] . "]"
                    );
                }
                break;
        }

        $output = apply_filter($output, "before_content");
        $output = apply_filter($output, "content");
        $output = apply_filter($output, "after_content");
        echo optimizeHtml($output);
    }

    public static function getHomepageOwner(): string
    {
        $homepage_title = Settings::getLanguageSetting(
            "homepage_owner",
            getFrontendLanguage()
        );
        return _esc($homepage_title);
    }

    public static function homepageOwner(): void
    {
        echo self::getHomepageOwner();
    }

    public static function footer(): void
    {
        do_event("enqueue_frontend_footer_scripts");
        enqueueScriptFile("lib/js/global.js");
        combinedScriptHtml();

        do_event("frontend_footer");
    }

    public static function executeModuleTemplate(
        string $module,
        string $template
    ): string {
        $retval = '';
        $originalTemplatePath = getModulePath($module, true) . "templates/" .
                $template;
        $ownTemplatePath = getTemplateDirPath(get_theme(), true) . $module
                . '/' . $template;

        if (!str_ends_with($template, ".php")) {
            $originalTemplatePath .= ".php";
            $ownTemplatePath .= ".php";
        }
        ob_start();
        if (is_file($ownTemplatePath)) {
            require $ownTemplatePath;
        } elseif (is_file($originalTemplatePath)) {
            require $originalTemplatePath;
        } else {
            $retval = ob_get_clean();
            throw new FileNotFoundException("Template " . $module . '/' . $template
                            . " not found!");
        }
        $retval = trim(ob_get_clean());
        return optimizeHtml($retval);
    }

    public static function escape($value): void
    {
        echo self::getEscape($value);
    }

    public static function getEscape($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
    }

    public static function logo(): void
    {
        if (Settings::get("logo_disabled") != "no") {
            return;
        }

        $logo_storage_url = self::getLogoUrl();
        $logo_storage_path = ULICMS_ROOT . "/content/images/" .
                Settings::get("logo_image");

        if (Settings::get("logo_disabled") == "no" && is_file($logo_storage_path)) {
            echo '<img class="website_logo" src="' . $logo_storage_url .
            '" alt="' . _esc(Settings::get("homepage_title")) . '"/>';
        }
    }

    public static function getLogoUrl(): string
    {
        return "content/images/" . Settings::get("logo_image");
    }

    // get current year
    public static function getYear(string $format = "Y"): string
    {
        return date($format);
    }

    public static function year(string $format = "Y"): void
    {
        echo self::getYear($format);
    }

    public static function getSiteSlogan(): string
    {
        // Existiert ein Motto für diese Sprache?
        // z.B. site_slogan_en
        $site_slogan = Settings::get("site_slogan_" . getFrontendLanguage());

        // Ansonsten Standard Motto
        if (!$site_slogan) {
            $site_slogan = Settings::get("site_slogan");
        }
        return _esc($site_slogan);
    }

    public static function siteSlogan(): void
    {
        echo self::getSiteSlogan();
    }

    public static function getMotto(): string
    {
        return self::getSiteSlogan();
    }

    public static function motto(): void
    {
        self::siteSlogan();
    }

    public static function executeDefaultOrOwnTemplate(
        string $template
    ): string {
        $retval = '';
        $originalTemplatePath = ULICMS_ROOT . "/default/" . $template;
        $ownTemplatePath = getTemplateDirPath(get_theme()) . '/' . $template;

        if (!str_ends_with($template, ".php")) {
            $originalTemplatePath .= ".php";
            $ownTemplatePath .= ".php";
        }

        ob_start();
        if (is_file($ownTemplatePath)) {
            require $ownTemplatePath;
        } elseif (is_file($originalTemplatePath)) {
            require $originalTemplatePath;
        } else {
            $retval = ob_get_clean();
            throw new FileNotFoundException(
                "Template " . $template . " not found!"
            );
        }
        $retval = ob_get_clean();
        return optimizeHtml($retval);
    }

    public static function headline($format = "<h1>%title%</h1>"): void
    {
        echo self::getHeadline($format);
    }

    public static function getHeadline($format = "<h1>%title%</h1>"): ?string
    {
        $id = get_ID();
        if (!$id) {
            return str_replace("%title%", get_title(null, true), $format);
        }
        $sql = "SELECT show_headline FROM " . tbname("content") .
                " where id = $id";
        $result = Database::query($sql);
        $dataset = Database::fetchObject($result);

        return $dataset->show_headline ?
                str_replace("%title%", get_title(null, true), $format) : null;
    }

    public static function doctype(): void
    {
        echo self::getDoctype();
    }

    public static function getDoctype(): string
    {
        return '<!doctype html>';
    }

    public static function ogHTMLPrefix(): void
    {
        echo self::getOgHTMLPrefix();
    }

    public static function getOgHTMLPrefix(): string
    {
        $language = getCurrentLanguage();
        return "<html prefix=\"og: http://ogp.me/ns#\" lang=\"$language\">";
    }

    public static function renderPartial(
        string $template,
        ?string $theme = null
    ): string {
        if (!$theme) {
            $theme = get_theme();
        }

        $file = getTemplateDirPath($theme, true) . "partials/{$template}";
        $file = !str_ends_with($file, ".php") ? $file . ".php" : $file;
        if (!is_file($file)) {
            throw new FileNotFoundException("Partial Template {$template} "
                            . "of Theme {$theme} not found.");
        }
        ob_start();
        require $file;
        $result = trim(ob_get_clean());
        return $result;
    }

    public static function getHtml5Doctype(): string
    {
        return "<!doctype html>";
    }

    public static function html5Doctype(): void
    {
        echo self::getHtml5Doctype();
    }

    public static function getBaseMetas(): string
    {
        ob_start();
        self::baseMetas();
        return ob_get_clean();
    }

    public static function baseMetas(): void
    {
        $title_format = Settings::get('title_format');
        if ($title_format) {
            $title = $title_format;
            $title = str_ireplace(
                "%homepage_title%",
                get_homepage_title(),
                $title
            );
            $title = str_ireplace("%title%", get_title(), $title);
            $title = str_ireplace("%motto%", get_site_slogan(), $title);
            $title = apply_filter($title, "title_tag");
            echo "<title>" . $title . "</title>";
        }

        echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>';
        echo '<meta charset="utf-8"/>';

        if (!Settings::get("disable_no_format_detection")) {
            echo '<meta name="format-detection" content="telephone=no"/>';
        }

        $dir = dirname($_SERVER['SCRIPT_NAME']);
        $dir = str_replace("\\", '/', $dir);

        if (str_ends_with($dir, '/') == false) {
            $dir .= '/';
        }

        $robots = null;

        // if the robots value is set for the current
        // page, use it as robots meta tag
        // else do fallback to the default setting
        try {
            $page = ContentFactory::getCurrentPage();
            $robots = $page && $page->robots ? $page->robots : Settings::get("robots");
        } catch (DatasetNotFoundException $e) {
            $robots = Settings::get("robots");
        }

        if ($robots) {
            $robots = apply_filter($robots, "meta_robots");
            echo '<meta name="robots" content="' . $robots . '"/>';
        }
        if (!Settings::get("hide_meta_generator")) {
            echo Template::executeDefaultOrOwnTemplate("powered-by");
            echo '<meta name="generator" content="UliCMS ' . cms_version() . '"/>';
        }
        output_favicon_code();

        if (!Settings::get("hide_shortlink") and (is_200() or is_403())) {
            $shortlink = get_shortlink();
            if ($shortlink) {
                echo '<link rel="shortlink" href="' . $shortlink . '"/>';
            }
        }

        if (!Settings::get("hide_canonical") and (is_200() or is_403())) {
            $canonical = get_canonical();
            if ($canonical) {
                echo '<link rel="canonical"  href="' . $canonical . '"/>';
            }
        }

        if (!Settings::get("no_autoembed_core_css")) {
            enqueueStylesheet("lib/css/core.scss");
        }
        do_event("enqueue_frontend_stylesheets");

        combinedStylesheetHtml();

        $min_style_file = getTemplateDirPath(get_theme()) .
                "style.min.css";
        $min_style_file_realpath = getTemplateDirPath(get_theme(), true) .
                "style.min.css";
        $style_file = getTemplateDirPath(get_theme()) . "style.css";
        $style_file_realpath = getTemplateDirPath(get_theme(), true) .
                "style.css";
        if (is_file($style_file_realpath)) {
            $style_file .= "?time=" . File::getLastChanged(
                $style_file_realpath
            );
            if (is_file($min_style_file_realpath)) {
                echo "<link rel=\"stylesheet\" type=\"text/css\" "
                . "href=\"$min_style_file\"/>";
            } elseif (is_file($style_file_realpath)) {
                echo "<link rel=\"stylesheet\" type=\"text/css\" "
                . "href=\"$style_file\"/>";
            }
        }

        $description = get_meta_description() ?
                get_meta_description() : Settings::get("meta_description");

        if ($description != '' && $description != false) {
            $description = apply_filter($description, "meta_description");
            $$description = _esc($description);
            if (!Settings::get("hide_meta_description")) {
                echo '<meta name="description" content="'
                . $description . '"/>';
            }
        }

        if (!Settings::get("disable_custom_layout_options")) {
            $font = Settings::get("default_font");

            $cssCode = "body{
font-family: " . $font . ";
font-size: " . Settings::get("font-size") . ";
background-color: " . Settings::get("body-background-color") . ";
color: " . Settings::get("body-text-color") . ";
}";

            $disableFunctions = getThemeMeta(
                get_theme(),
                "disable_functions"
            );

            $minifier = new Minify\CSS();
            $minifier->add($cssCode);
            if (!(is_array($disableFunctions)
                    and in_array(
                        "output_design_settings_styles",
                        $disableFunctions
                    ))
            ) {
                echo App\HTML\Style::fromString($minifier->minify());
            }

            if (Settings::get("video_width_100_percent")) {
                echo "<style>
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

    public static function jQueryScript(): void
    {
        $jQueryurl = get_jquery_url();
        echo Script::fromFile($jQueryurl);
        do_event("after_jquery_include");
    }

    public static function getjQueryScript(): string
    {
        ob_start();
        self::jQueryScript();
        return ob_get_clean();
    }

    public static function content(): void
    {
        echo self::getContent();
    }

    // TODO: Refactor this method
    public static function getContent(): string
    {
        $theme = get_theme();

        $errorPage403 = intval(
            Settings::getLanguageSetting(
                "error_page_403",
                getCurrentLanguage()
            )
        );
        $errorPage404 = intval(
            Settings::getLanguageSetting(
                "error_page_404",
                getCurrentLanguage()
            )
        );

        $slug = get_slug();

        $content = null;
        if (is_200()) {
            $content = ContentFactory::getBySlugAndLanguage(
                $slug,
                getCurrentLanguage()
            );

            if (!is_logged_in()) {
                db_query("UPDATE " . tbname("content") .
                        " SET views = views + 1 WHERE slug='" .
                        Database::escapeValue(get_slug()) .
                        "' AND language='" . db_escape(getFrontendLanguage())
                        . "'");
            }
        } elseif (is_404()) {
            if ($errorPage404) {
                $content = ContentFactory::getByID($errorPage404);
            } else {
                return get_translation('PAGE_NOT_FOUND_CONTENT');
            }
        } elseif (is_403()) {
            $theme = Settings::get('theme');
            if ($errorPage403) {
                $content = ContentFactory::getByID($errorPage403);
            } else {
                return get_translation('FORBIDDEN_COTENT');
            }
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
        if (!(isset($data["disable_shortcodes"]) && $data["disable_shortcodes"])) {
            $htmlContent = replaceShortcodesWithModules($htmlContent);
            $htmlContent = replaceOtherShortCodes($htmlContent);
        }
        $htmlContent = apply_filter($htmlContent, "content");
        return trim($htmlContent);
    }

    public static function languageSelection()
    {
        $result = db_query("SELECT language_code, name FROM " .
                tbname("languages") . " ORDER by name");
        echo "<ul class='language_selection'>";
        while ($row = db_fetch_object($result)) {
            $domain = getDomainByLanguage($row->language_code);
            if ($domain) {
                echo "<li>" . "<a href='http://" . $domain . "'>" .
                $row->name . "</a></li>";
            } else {
                echo "<li>" . "<a href='./?language=" . $row->language_code
                . "'>" . $row->name . "</a></li>";
            }
        }
        echo "</ul>";
    }

    public static function _getLanguageSelection(): string
    {
        ob_start();
        self::languageSelection();
        return ob_get_clean();
    }

    public static function getPoweredByUliCMS(): string
    {
        return get_translation('powered_by_ulicms');
    }

    // Gibt "Diese Seite läuft mit UliCMS" aus
    public static function poweredByUliCMS(): void
    {
        echo self::getPoweredByUliCMS();
    }

    public static function getComments(): string
    {
        if (!is_200()) {
            return "";
        }

        return Template::executeModuleTemplate(
            'core_comments',
            'comments.php'
        );
    }

    public static function comments(): void
    {
        echo self::getComments();
    }

    public static function getEditButton(): string
    {
        $html = '';
        if (!is_logged_in()) {
            return $html;
        }
        $acl = new PermissionChecker(get_user_id());
        if ($acl->hasPermission('pages') && Vars::getNoCache() && is_200()) {
            $id = get_ID();
            $page = ContentFactory::getById($id);
            if (in_array($page->language, getAllLanguages(true))) {
                $html .= '<div class="ulicms-edit">';
                $html .= App\HTML\Link::actionLink(
                    "pages_edit",
                    get_translation("edit"),
                    "page={$id}",
                    [
                        "class" => "btn btn-warning btn-edit"
                    ]
                );
                $html .= "</div>";
            }
        }

        return $html;
    }

    public static function editButton(): void
    {
        echo self::getEditButton();
    }

    public static function getFooterText(): string
    {
        return replaceShortcodesWithModules(Settings::get("footer_text"), true);
    }

    public static function footerText(): void
    {
        echo self::getFooterText();
    }
}
