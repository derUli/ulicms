<?php

declare(strict_types=1);

use App\Models\Content\Language;
use App\Models\Content\Categories;
use App\Utils\File;
use App\Exceptions\DatasetNotFoundException;

function html5_doctype(): void
{
    echo Template::getHtml5Doctype();
}

function get_html5_doctype(): string
{
    return Template::getHtml5Doctype();
}

function og_html_prefix(): void
{
    echo Template::getOgHTMLPrefix();
}

function get_og_html_prefix(): string
{
    return Template::getOgHTMLPrefix();
}

function og_tags(): void
{
    echo get_og_tags();
}

function get_og_tags(?string $slug = null): string
{
    $html = '';
    if (is_200()) {
        $og_data = get_og_data($slug);
        $og_title = $og_data["og_title"];
        $og_image = $og_data["og_image"];
        $og_description = $og_data["og_description"];
        $og_url = getCurrentURL();

        // Falls kein og_title für die Seite gesetzt ist,
        // Standardtitel bzw. Headline verwenden
        if (is_null($og_title) or empty($og_title)) {
            $og_title = get_headline();
        }

        if (is_null($og_image) or empty($og_image)) {
            $og_image = Settings::get("og_image");
        }

        if (!empty($og_image) && !str_starts_with($og_image, "http")) {
            $og_image = ModuleHelper::getBaseUrl() . ltrim($og_image, '/');
        }
        $page = get_page($slug);
        if (empty($og_image) &&
                !StringHelper::isNullOrWhitespace($page["article_image"])) {
            $og_image = ltrim($page["article_image"], '/');
        }
        if (!empty($og_image) && !str_starts_with($og_image, "http")) {
            $og_image = ModuleHelper::getBaseUrl() . ltrim($og_image, '/');
        }
        if (is_null($og_description) or empty($og_description)) {
            $og_description = get_meta_description();
        }

        $og_title = apply_filter($og_title, "og_title");
        $og_type = apply_filter("article", "og_type");
        $og_url = apply_filter($og_url, "og_url");
        $og_image = apply_filter($og_image, "og_image");
        $og_description = apply_filter($og_description, "og_description");

        if ($og_title) {
            $html .= '<meta property="og:title" content="'
                    . _esc($og_title) . '" />';
        }

        if ($og_description) {
            $html .= '<meta property="og:description" content="'
                    . _esc($og_description) . '" />';
        }

        if ($og_type) {
            $html .= '<meta property="og:type" content="'
                    . _esc($og_type) . '" />';
        }

        if ($og_url) {
            $html .= '<meta property="og:url" content="'
                    . _esc($og_url) . '" />';
        }

        if ($og_image) {
            $html .= '<meta property="og:image" content="'
                    . _esc($og_image) . '" />';
        }

        $html .= '<meta property="og:site_name" content="'
                . get_homepage_title() . '" />';
    }

    $html = apply_filter($html, "og_html");
    return $html;
}

function get_og_data($slug = ''): ?array
{
    if (empty($slug)) {
        $slug = isset($_GET["slug"]) ? $_GET["slug"] : get_frontpage();
    }

    $data = null;

    $result = db_query("SELECT og_title, og_image, og_description FROM " .
            tbname("content") . " WHERE slug='" . db_escape($slug) .
            "' AND language='" . db_escape(getFrontendLanguage()) . "'");
    if (db_num_rows($result) > 0) {
        $data = db_fetch_assoc($result);
    }
    return $data;
}

function get_all_combined_html(): string
{
    $html = '';
    $html .= getCombinedStylesheetHtml();
    $html .= getCombinedScriptHtml();
    return $html;
}

function edit_button(): void
{
    Template::editButton();
}

function get_edit_button(): ?string
{
    return Template::getEditButton();
}

function all_combined_html(): void
{
    echo get_all_combined_html();
}

function get_ID(): ?int
{
    if (Vars::get("id") !== null) {
        return Vars::get("id");
    }

    $page = get_slug();

    $dataset = null;

    $sql = "SELECT `id` FROM " . tbname("content") .
            " WHERE slug='" . db_escape($page) .
            "'  AND language='" . db_escape(getFrontendLanguage()) . "'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $dataset = (int) $dataset->id;
    }
    Vars::set("id", $dataset);
    return $dataset;
}

function is_active(): bool
{
    if (Vars::get("active") !== null) {
        return Vars::get("active");
    }

    $page = get_slug();

    $dataset = true;

    $sql = "SELECT `active` FROM " . tbname("content")
            . " WHERE slug='" . db_escape($page) . "'  AND language='" .
            db_escape(getFrontendLanguage()) . "'";
    $result = db_query($sql);

    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $dataset = boolval($dataset->active);
    }

    Vars::set("active", $dataset);
    return $dataset;
}

function get_type(?string $slug = null, ?string $language = null): ?string
{
    if (!$slug) {
        $slug = get_slug();
    }

    if (!$language) {
        $language = getCurrentLanguage();
    }
    $varName = "type_{$slug}_{$language}";
    if (Vars::get($varName)) {
        return Vars::get($varName);
    }

    $type = null;

    try {
        $page = ContentFactory::getBySlugAndLanguage($slug, $language);
        $type = $page->type;
    } catch (DatasetNotFoundException $e) {
        $type = null;
    }

    $result = apply_filter($type, "get_type");
    Vars::set($varName, $type);
    return $result;
}

function get_article_meta(?string $page = null): ?object
{
    if (!$page) {
        $page = get_slug();
    }

    $dataset = '';
    $sql = "SELECT `article_author_name`, `article_author_email`, CASE WHEN "
            . "`article_date` is not null then UNIX_TIMESTAMP(article_date) "
            . "else null end as article_date, `article_image`, "
            . "`excerpt` FROM " . tbname("content") .
            " WHERE slug='" . db_escape($page) .
            "'  AND language='" .
            Database::escapeValue(getFrontendLanguage()) . "'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = Database::fetchObject($result);
        $dataset->excerpt = $dataset->excerpt ? replaceShortcodesWithModules($dataset->excerpt) : "";
        $dataset->article_date = $dataset->article_date ?
                intval($dataset->article_date) : null;
    }
    $dataset = apply_filter($dataset, "get_article_meta");
    return $dataset;
}

function get_cache_control(): string
{
    if (Vars::get("cache_control") !== null) {
        return Vars::get("cache_control");
    }
    $page = get_slug();

    $cacheControl = "auto";
    $sql = "SELECT `cache_control` FROM " . tbname("content") .
            " WHERE slug='" . db_escape($page) . "'  AND language='" .
            db_escape(getFrontendLanguage()) . "'";
    $result = db_query($sql);
    $dataset = null;

    if ($result and db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $cacheControl = $dataset->cache_control ? $dataset->cache_control : $cacheControl;
    }
    $cacheControl = apply_filter($cacheControl, "get_cache_control");
    Vars::set("cache_control", $cacheControl);
    return $cacheControl;
}

function get_text_position(): string
{
    $page = get_slug();

    $dataset = null;
    $sql = "SELECT `text_position` FROM " . tbname("content") .
            " WHERE slug='" . db_escape($page) .
            "'  AND language='" . db_escape(getFrontendLanguage()) . "'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $dataset = $dataset->text_position;
    }
    if (empty($dataset)) {
        $dataset = "before";
    }
    return $dataset;
}

function get_category_id(string $page = null): ?int
{
    if (!$page) {
        $page = get_slug();
    }
    $dataset = null;
    $sql = "SELECT `category_id` FROM " . tbname("content") .
            " WHERE slug='" . db_escape($page) . "'  AND language='" .
            db_escape(getCurrentLanguage()) . "'";
    $result = db_query($sql);
    if ($result and db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $dataset = intval($dataset->category_id);
    }
    return $dataset;
}

function category_id(?string $page = null): void
{
    echo get_category_id($page);
}

function get_parent(string $page = null): ?int
{
    if (!$page) {
        $page = get_slug();
    }
    $parent_id = null;
    $sql = "SELECT `parent_id` FROM " . tbname("content") . " WHERE slug='"
            . db_escape($page) . "'  AND language='" .
            db_escape(getFrontendLanguage()) . "'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $parent_id = $dataset->parent_id ? intval($dataset->parent_id) : null;
    }
    return $parent_id;
}

function include_jquery(): void
{
    Template::jQueryScript();
}

function get_access(?string $page = null): array
{
    $access = [];
    if (!$page) {
        $page = get_slug();
    }

    $sql = "SELECT `access` FROM " . tbname("content") .
            " WHERE slug='" . db_escape($page) .
            "'  AND language='" . db_escape(getFrontendLanguage()) . "'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $access = explode(",", $dataset->access);
    }

    return $access;
}

function get_redirection(?string $page = null): ?string
{
    if (!$page) {
        $page = get_slug();
    }
    $sql = "SELECT `link_url` FROM " . tbname("content") .
            " WHERE slug='" . db_escape($page) . "'  AND language='" .
            db_escape(getFrontendLanguage()) . "' and type='link'";
    $result = db_query($sql);

    $redirection = null;
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        if (!empty($dataset->link_url) && !is_null($dataset->link_url)) {
            $redirection = $dataset->link_url;
        }
    }
    return $redirection;
}

function get_theme(?string $page = null): ?string
{
    if (!$page) {
        $page = get_slug();
    }

    if (Vars::get("theme_" . $page) !== null) {
        return Vars::get("theme_" . $page);
    }
    $theme = Settings::get('theme');
    $mobile_theme = Settings::get("mobile_theme");
    if ($mobile_theme and is_mobile()) {
        $theme = $mobile_theme;
    }

    if (is_200()) {
        $sql = "SELECT `theme` FROM " . tbname("content") . " WHERE slug='" .
                db_escape($page) . "'  AND language='" .
                db_escape(getFrontendLanguage()) . "'";
        $result = db_query($sql);
        if ($result and db_num_rows($result) > 0) {
            $data = db_fetch_object($result);
            if ($data->theme) {
                $theme = $data->theme;
            }
        }
    }
    $theme = apply_filter($theme, "theme");
    Vars::set("theme_" . $page, $theme);
    return $theme;
}

function language_selection(): void
{
    Template::languageSelection();
}

function get_category(): string
{
    $current_page = get_page();
    if (!(isset($current_page["category_id"]) and
            $current_page["category_id"])) {
        return '';
    }
    return Categories::getCategoryById(
        intval($current_page["category_id"])
    ) ?? '';
}

function category(): void
{
    Template::escape(get_category());
}

function get_body_classes(): string
{
    return Template::getBodyClasses();
}

function body_classes(): void
{
    echo Template::getBodyClasses();
}

// Gibt "Diese Seite läuft mit UliCMS" aus
function poweredByUliCMS(): void
{
    Template::poweredByUliCMS();
}

// Einen zufälligen Banner aus der Datenbank ausgeben
function random_banner(): void
{
    Template::randomBanner();
}

function logo(): void
{
    Template::logo();
}

function year($format = "Y"): void
{
    Template::year($format);
}

function homepage_owner(): void
{
    Template::homepageOwner();
}

function get_homepage_title(): string
{
    $homepage_title = Settings::get("homepage_title_" . getFrontendLanguage());
    if (!$homepage_title) {
        $homepage_title = Settings::get("homepage_title");
    }
    return _esc($homepage_title);
}

function homepage_title(): void
{
    echo get_homepage_title();
}

function get_meta_description(?string $ipage = null): string
{
    $ipage = isset($_GET["slug"]) ? db_escape($_GET["slug"]) : '';
    $result = db_query("SELECT meta_description FROM " . tbname("content") .
            " WHERE slug='$ipage' AND language='" .
            db_escape(getFrontendLanguage()) . "'");

    if (db_num_rows($result) > 0) {
        while ($row = db_fetch_object($result)) {
            if (!empty($row->meta_description)) {
                return $row->meta_description;
            }
        }
    }
    $meta_description = Settings::get("meta_description_" .
                    getFrontendLanguage());
    if (!$meta_description) {
        $meta_description = Settings::get("meta_description");
    }

    return $meta_description;
}

function meta_description(): void
{
    $value = get_meta_description();
    if ($value) {
        echo $value;
    }
}

function get_title(?string $slug = null, bool $headline = false): string
{
    $cacheVar = $headline ? "headline" : "title";
    if (Vars::get($cacheVar)) {
        return Vars::get($cacheVar);
    }

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

    if (is_404()) {
        if ($errorPage404) {
            $content = ContentFactory::getByID($errorPage404);
            if ($content->id !== null) {
                return $content->getHeadline();
            }
        }
        return get_translation("page_not_found");
    } elseif (is_403()) {
        if ($errorPage403) {
            $content = ContentFactory::getByID($errorPage403);
            if ($content->id !== null) {
                return $content->getHeadline();
            }
        }
        return get_translation("forbidden");
    }

    $slug = isset($_GET["slug"]) ? db_escape($_GET["slug"]) : "";
    $result = db_query("SELECT alternate_title, title FROM " .
            tbname("content") . " WHERE slug='$slug' AND language='" .
            db_escape(getFrontendLanguage()) . "'");

    if (db_num_rows($result) > 0) {
        while ($row = db_fetch_object($result)) {
            if ($headline and isset($row->alternate_title) && !empty($row->alternate_title)) {
                $title = $row->alternate_title;
            } else {
                $title = $row->title;
            }

            $title = apply_filter($title, "title");
            $title = Template::getEscape($title);
            Vars::set($cacheVar, $title);
            return $title;
        }
    }
    return '';
}

function title(?string $ipage = null, bool $headline = false): void
{
    echo get_title($ipage, $headline);
}

function get_headline(?string $ipage = null): string
{
    return get_title($ipage, true);
}

function headline(?string $ipage = null): void
{
    echo get_headline($ipage);
}

function apply_filter($text, string $type)
{
    $modules = getAllModules();
    $disabledModules = Vars::get("disabledModules") ?? [];

    $modulesCount = count($modules);
    for ($i = 0; $i < $modulesCount; $i++) {
        if (in_array($modules[$i], $disabledModules)) {
            continue;
        }
        $module_content_filter_file1 = getModulePath($modules[$i], true)
                . $modules[$i] . "_" . $type . "_filter.php";
        $module_content_filter_file2 = getModulePath($modules[$i], true)
                . "filters/" . $type . ".php";

        $main_class = getModuleMeta($modules[$i], "main_class");
        $controller = null;
        if ($main_class) {
            $controller = ControllerRegistry::get($main_class);
        }
        $escapedName = ModuleHelper::underscoreToCamel($type . "_filter");
        if ($controller and method_exists($controller, $escapedName)) {
            $text = $controller->$escapedName($text);
        } elseif (is_file($module_content_filter_file1)) {
            require_once $module_content_filter_file1;
            if (function_exists($modules[$i] . "_" . $type . "_filter")) {
                $text = call_user_func($modules[$i] . "_" . $type .
                        "_filter", $text);
            }
        } elseif (is_file($module_content_filter_file2)) {
            require_once $module_content_filter_file2;
            if (function_exists($modules[$i] . "_" . $type . "_filter")) {
                $text = call_user_func($modules[$i] . "_" . $type .
                        "_filter", $text);
            }
        }
    }

    return $text;
}

function get_site_slogan(): string
{
    return Template::getSiteSlogan();
}

function site_slogan(): void
{
    echo Template::siteSlogan();
}

function motto(): void
{
    site_slogan();
}

function get_motto(): string
{
    return get_site_slogan();
}

function get_frontpage(): ?string
{
    setLanguageByDomain();
    if (getFrontendLanguage()) {
        $frontpage = Settings::get("frontpage_" . getFrontendLanguage());
        if ($frontpage) {
            return $frontpage;
        }
    }
    return Settings::get("frontpage");
}

function get_slug(): string
{
    return !empty($_GET["slug"]) ? $_GET["slug"] : get_frontpage();
}

function get_requested_pagename(): string
{
    return get_slug();
}

function set_requested_pagename(
    string $slug,
    ?string $language = null
): void {
    if (!$language) {
        $language = getCurrentLanguage();
    }
    $_GET["slug"] = $slug;
    $_REQUEST["slug"] = $slug;

    $_GET['language'] = $language;
    $_REQUEST['language'] = $language;
    $_SESSION['language'] = $language;
}

function is_home(): bool
{
    return get_slug() === get_frontpage();
}

function is_frontpage(): bool
{
    return is_home();
}

function is_200(): bool
{
    return check_status() == "200 OK";
}

function is_403(): bool
{
    return check_status() == "403 Forbidden";
}

function is_404(): bool
{
    return check_status() == "404 Not Found";
}

function buildtree(
    array $src_arr,
    ?int $parent_id = 0,
    ?array $tree = []
): array {
    foreach ($src_arr as $idx => $row) {
        if ($row['parent_id'] == $parent_id) {
            foreach ($row as $k => $v) {
                $tree[$row['id']][$k] = $v;
            }
            unset($src_arr[$idx]);
            $tree[$row['id']]['children'] = buildtree(
                $src_arr,
                intval(
                    $row['id']
                )
            );
        }
    }
    ksort($tree);
    return $tree;
}

function parent_item_contains_current_page(?int $id): bool
{
    $retval = false;
    if (!$id) {
        return $retval;
    }
    $id = (int) $id;
    $language = $_SESSION['language'];
    $sql = "SELECT id, slug, parent_id FROM " . tbname("content") . " WHERE language = '$language' AND active = 1 AND `deleted_at` IS NULL";
    $r = db_query($sql);

    $data = [];
    while ($row = db_fetch_assoc($r)) {
        $data[] = $row;
    }

    $tree = buildtree($data, $id);
    foreach ($tree as $key) {
        if ($key["slug"] == get_slug()) {
            $retval = true;
        }
    }
    return $retval;
}

function get_menu(
    string $name = "top",
    ?int $parent_id = null,
    bool $recursive = true,
    string $order = "position"
): string {
    $html = '';
    $name = db_escape($name);
    $language = $_SESSION['language'];
    $sql = "SELECT id, slug, access, link_url, title, "
            . "alternate_title, menu_image, target, type, link_to_language, position FROM " . tbname("content") . " WHERE menu='$name' AND language = '$language' AND active = 1 AND `deleted_at` IS NULL AND hidden = 0 and type <> 'snippet' and parent_id ";

    if ($parent_id === null) {
        $sql .= " IS NULL ";
    } else {
        $sql .= " = " . intval($parent_id) . " ";
    }
    $sql .= " ORDER by " . $order;
    $result = db_query($sql);

    if (db_num_rows($result) == 0) {
        return $html;
    }

    if ($parent_id === null) {
        $html .= "<ul class='menu_" . $name . " navmenu'>\n";
    } else {
        $containsCurrentItem = parent_item_contains_current_page(intval($parent_id));

        $classes = "sub_menu";

        if ($containsCurrentItem) {
            $classes .= " contains-current-page";
        }
        $html .= "<ul class='" . $classes . "'>\n";
    }

    while ($row = db_fetch_object($result)) {
        if (checkAccess($row->access)) {
            $containsCurrentItem = parent_item_contains_current_page((int) $row->id);

            $additional_classes = " menu-link-to-" . $row->id . " ";
            if ($containsCurrentItem) {
                $additional_classes .= "contains-current-page ";
            }

            if (get_slug() != $row->slug) {
                $html .= "  <li class='" . trim($additional_classes) . "'>";
            } else {
                $html .= "  <li class='menu_active_list_item" . rtrim($additional_classes) . "'>";
            }

            $title = $row->title;

            // Show page positions in menu if user has the "pages_show_positions" permission.
            if (is_logged_in()) {
                $acl = new ACL();
                if ($acl->hasPermission("pages_show_positions") and Settings::get("user/" . get_user_id() . "/show_positions")) {
                    $title .= " ({$row->position})";
                }
            }

            $redirection = $row->link_url;
            if ($row->type == "language_link" && !is_null($row->link_to_language)) {
                $language = new Language($row->link_to_language);
                $redirection = $language->getLanguageLink();
            }
            // if content has type link or node url is the target url else build seo url
            $url = ($row->type == "link" or $row->type == "node") ? $row->link_url : buildSEOUrl($row->slug);
            $url = Template::getEscape($url);

            if (get_slug() != $row->slug) {
                $html .= "<a href='" . $url . "' target='" . $row->target . "' class='" . trim($additional_classes) . "'>";
            } else {
                $html .= "<a class='menu_active_link" . rtrim($additional_classes) . "' href='" . $url . "' target='" . $row->target . "'>";
            }
            if (!is_null($row->menu_image) && !empty($row->menu_image)) {
                $html .= '<img src="' . $row->menu_image . '" alt="' . _esc($title) . '"/>';
            } else {
                $html .= _esc($title);
            }
            $html .= "</a>\n";

            if ($recursive) {
                $html .= get_menu($name, (int) $row->id, true, $order);
            }

            $html .= "</li>";
        }
    }
    $html .= "</ul>";
    return $html;
}

function menu(
    string $name = "top",
    ?int $parent = null,
    bool $recursive = true,
    string $order = 'position'
): void {
    echo get_menu($name, $parent, $recursive, $order);
}

function output_favicon_code(): void
{
    echo get_output_favicon_code();
}

function get_output_favicon_code(): string
{
    $url = "content/images/favicon.ico";

    $path = ULICMS_ROOT . "/content/images/favicon.ico";
    $html = '';
    if (file_exists($path)) {
        $url .= "?time=" . File::getLastChanged($path);
        $html = '<link rel="icon" href="' . $url . '" type="image/x-icon" />' . '<link rel="shortcut icon" href="' . $url . '" type="image/x-icon" />';
    }
    return $html;
}

function base_metas(): void
{
    Template::baseMetas();
}

function get_base_metas(): string
{
    return Template::getBaseMetas();
}

function head(): void
{
    base_metas();
}

function get_head(): string
{
    return get_base_metas();
}

function get_page(?string $slug = ''): ?array
{
    if (empty($slug)) {
        $slug = isset($_GET["slug"]) ? $_GET["slug"] : "";
    }
    if (empty($slug)) {
        $slug = get_frontpage();
    }
    if (Vars::get("page_" . $slug)) {
        return Vars::get("page_" . $slug);
    }
    $result = db_query("SELECT * FROM " . tbname("content") . " WHERE slug='" . db_escape($slug) . "' AND language='" . db_escape(getFrontendLanguage()) . "'");
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_assoc($result);
        Vars::set("page_" . $slug, $dataset);
        return $dataset;
    } else {
        Vars::set("page_" . $slug, null);
        return null;
    }
}

function content(): void
{
    Template::content();
}

function get_content(): string
{
    return Template::getContent();
}

function checkforAccessForDevice(string $access): bool
{
    $access = explode(",", $access);
    $allowed = false;
    if (in_array("mobile", $access) && is_mobile()) {
        $allowed = true;
    }
    if (in_array("desktop", $access) && !is_mobile()) {
        $allowed = true;
    }
    if (!in_array("mobile", $access) && !in_array("desktop", $access)) {
        $allowed = true;
    }
    return $allowed;
}

function checkAccess(string $access = ''): ?string
{
    $access_for_device = checkforAccessForDevice($access);
    if (!$access_for_device) {
        return null;
    }
    $access = explode(",", $access);

    if (in_array("all", $access)) {
        return "all";
    }

    if (in_array("registered", $access) and is_logged_in()) {
        return "registered";
    }

    $accessCount = count($access);

    for ($i = 0; $i < $accessCount; $i++) {
        if (is_numeric($access[$i]) and isset($_SESSION['group_id']) and $access[$i] == $_SESSION['group_id']) {
            return $access[$i];
        }
    }
    return null;
}

function check_status(): string
{
    $status = apply_filter("", "status");
    if (!empty($status)) {
        return $status;
    }

    if (isMaintenanceMode()) {
        return "503 Service Unavailable";
    }
    if (get_type() == "snippet") {
        return "403 Forbidden";
    }
    if (!(isset($_GET["slug"]) && !empty($_GET["slug"]))) {
        $_GET["slug"] = get_frontpage();
    }

    if (!is_active() && !is_logged_in()) {
        return "403 Forbidden";
    }

    $test = isset($_GET["slug"]) ? get_page($_GET["slug"]) : null;
    if (!$test || !is_null($test["deleted_at"])) {
        no_cache();
        return "404 Not Found";
    }

    $access = checkAccess($test["access"]);
    if ($access) {
        if ($access != "all") {
            no_cache();
        }
        return "200 OK";
    }
    no_cache();
    return "403 Forbidden";
}

function cms_release_year(): void
{
    $v = new UliCMSVersion();
    echo $v->getReleaseYear();
}
