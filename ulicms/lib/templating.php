<?php

declare(strict_types=1);

use UliCMS\Models\Content\Language;
use UliCMS\Models\Content\Categories;
use UliCMS\Models\Content\Types\DefaultContentTypes;
use UliCMS\Utils\File;

function html5_doctype(): void {
    echo Template::getHtml5Doctype();
}

function get_html5_doctype() {
    return Template::getHtml5Doctype();
}

function og_html_prefix(): void {
    echo Template::getOgHTMLPrefix();
}

function get_og_html_prefix(): string {
    return Template::getOgHTMLPrefix();
}

function og_tags(): void {
    echo get_og_tags();
}

function get_og_tags(?string $slug = null): string {
    $html = "";
    if (is_200()) {
        $og_data = get_og_data($slug);
        $og_title = $og_data["og_title"];
        $og_image = $og_data["og_image"];
        $og_description = $og_data["og_description"];
        $og_url = getCurrentURL();

        // Falls kein og_title für die Seite gesetzt ist, Standardtitel bzw. Headline verwenden
        if (is_null($og_title) or empty($og_title)) {
            $og_title = get_headline();
        }

        if (is_null($og_image) or empty($og_image)) {
            $og_image = Settings::get("og_image");
        }

        if (!empty($og_image) and ! startsWith($og_image, "http")) {
            $og_image = ModuleHelper::getBaseUrl() . ltrim($og_image, "/");
        }
        $page = get_page($slug);
        if (empty($og_image) and ! StringHelper::isNullOrWhitespace($page["article_image"])) {

            $og_image = ltrim($page["article_image"], "/");
        }
        if (!empty($og_image) and ! startsWith($og_image, "http")) {
            $og_image = ModuleHelper::getBaseUrl() . ltrim($og_image, "/");
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
            $html .= '<meta property="og:title" content="' . _esc($og_title) . '" />';
        }

        if ($og_description) {
            $html .= '<meta property="og:description" content="' . _esc($og_description) . '" />';
        }

        if ($og_type) {
            $html .= '<meta property="og:type" content="' . _esc($og_type) . '" />';
        }

        if ($og_url) {
            $html .= '<meta property="og:url" content="' . _esc($og_url) . '" />';
        }

        if ($og_image) {
            $html .= '<meta property="og:image" content="' . _esc($og_image) . '" />';
        }

        $html .= '<meta property="og:site_name" content="' . get_homepage_title() . '" />';
    }

    $html = apply_filter($html, "og_html");
    return $html;
}

function get_og_data($slug = "") {
    if (empty($slug)) {
        $slug = $_GET["seite"];
    }

    if (empty($slug)) {
        $slug = get_frontpage();
    }
    $result = db_query("SELECT og_title, og_image, og_description FROM " . tbname("content") . " WHERE slug='" . db_escape($slug) . "' AND language='" . db_escape($_SESSION["language"]) . "'");
    if (db_num_rows($result) > 0) {
        return db_fetch_assoc($result);
    }
    return null;
}

function get_all_combined_html() {
    $html = "";
    $html .= getCombinedStylesheetHtml();
    $html .= combinedScriptHtml();
    return $html;
}

function edit_button() {
    Template::editButton();
}

function get_edit_button() {
    Template::getEditButton();
}

function all_combined_html() {
    echo get_all_comined_html();
}

function get_ID(): ?int {
    if (!is_null(Vars::get("id"))) {
        return Vars::get("id");
    }

    $page = get_requested_pagename();

    $dataset = null;

    $sql = "SELECT `id` FROM " . tbname("content") . " WHERE slug='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $dataset = intval($dataset->id);
    }
    Vars::set("id", $dataset);
    return $dataset;
}

function is_active(): bool {
    if (!is_null(Vars::get("active"))) {
        return Vars::get("active");
    }
    if (!$page) {
        $page = get_requested_pagename();
    }
    $dataset = boolval(1);
    $sql = "SELECT `active` FROM " . tbname("content") . " WHERE slug='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $dataset = boolval($dataset->active);
    }
    Vars::set("active", $dataset);
    return $dataset;
}

function get_type(?string $slug = null, ?string $language = null): string {

    if (!$slug) {
        $slug = get_requested_pagename();
    }

    if (!$language) {
        $language = getCurrentLanguage();
    }
    $varName = "type_{$slug}_{$language}";
    if (Vars::get($varName)) {
        return Vars::get($varName);
    }
    try {
        $page = ContentFactory::getBySlugAndLanguage($slug, $language);
        $type = $page->type;
    } catch (Exception $e) {
        $type = "page";
    }

    $result = apply_filter($type, "get_type");
    Vars::set($varName, $type);
    return $result;
}

function get_article_meta(?string $page = null): ?object {
    if (!$page) {
        $page = get_requested_pagename();
    }
    $dataset = null;
    $sql = "SELECT `article_author_name`, `article_author_email`, CASE WHEN `article_date` is not null then UNIX_TIMESTAMP(article_date) else null end as article_date, `article_image`, `excerpt` FROM " . tbname("content") . " WHERE slug='" . db_escape($page) . "'  AND language='" . Database::escapeValue($_SESSION["language"]) . "'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = Database::fetchObject($result);
        $dataset->excerpt = replaceShortcodesWithModules($dataset->excerpt);
    }
    $dataset = apply_filter($dataset, "get_article_meta");
    return $dataset;
}

function get_cache_control(): string {
    if (!is_null(Vars::get("cache_control"))) {
        return Vars::get("cache_control");
    }
    $page = get_requested_pagename();

    $dataset = "";
    $sql = "SELECT `cache_control` FROM " . tbname("content") . " WHERE slug='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $result = db_query($sql);
    if ($result and db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $dataset = $dataset->cache_control;
    }
    if (empty($dataset)) {
        $dataset = "auto";
    }
    $dataset = apply_filter($dataset, "get_cache_control");
    Vars::set("cache_control", $dataset);
    return $dataset;
}

function get_text_position(): string {
    $page = get_requested_pagename();

    $dataset = null;
    $sql = "SELECT `text_position` FROM " . tbname("content") . " WHERE slug='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
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

function get_category_id(string $page = null): ?int {
    if (!$page) {
        $page = get_requested_pagename();
    }
    $dataset = null;
    $sql = "SELECT `category_id` FROM " . tbname("content") . " WHERE slug='" . db_escape($page) . "'  AND language='" . db_escape(getCurrentLanguage()) . "'";
    $result = db_query($sql);
    if ($result and db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $dataset = intval($dataset->category_id);
    }
    return $dataset;
}

function category_id(?string $page = null): int {
    echo get_category_id($page);
}

function get_parent(string $page = null): ?int {
    if (!$page) {
        $page = get_requested_pagename();
    }
    $dataset = null;
    $sql = "SELECT `parent_id` FROM " . tbname("content") . " WHERE slug='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $dataset = $dataset->parent_id;
    }
    if (empty($dataset)) {
        $dataset = null;
    }
    return $dataset;
}

function get_custom_data(?string $page = null): ?array {
    if (!$page) {
        $page = get_requested_pagename();
    }

    $sql = "SELECT `custom_data` FROM " . tbname("content") . " WHERE slug='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        return json_decode($dataset->custom_data, true);
    }
    return null;
}

function include_jquery(): void {
    Template::jQueryScript();
}

function get_access(?string $page = null): string {
    if (!$page) {
        $page = get_requested_pagename();
    }
    $sql = "SELECT `access` FROM " . tbname("content") . " WHERE slug='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $access = explode(",", $dataset->access);
        return $access;
    }
    return null;
}

function get_redirection(?string $page = null): ?string {
    if (!$page) {
        $page = get_requested_pagename();
    }
    $sql = "SELECT `redirection` FROM " . tbname("content") . " WHERE slug='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "' and type='link'";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        if (!empty($dataset->redirection) and ! is_null($dataset->redirection)) {
            return $dataset->redirection;
        }
        return null;
    }
    return null;
}

function get_theme(?string $page = null): ?string {
    if (!$page) {
        $page = get_requested_pagename();
    }

    if (!is_null(Vars::get("theme_" . $page))) {
        return Vars::get("theme_" . $page);
    }
    $theme = Settings::get("theme");
    $mobile_theme = Settings::get("mobile_theme");
    if ($mobile_theme and ! empty($mobile_theme) and is_mobile()) {
        $theme = $mobile_theme;
    }

    if (is_200()) {
        $sql = "SELECT `theme` FROM " . tbname("content") . " WHERE slug='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
        $result = db_query($sql);
        if ($result and db_num_rows($result) > 0) {
            $data = db_fetch_object($result);
            if (isset($data->theme) and ! empty($data->theme) and ! is_null($data->theme)) {
                $theme = $data->theme;
            }
        }
    }
    $theme = apply_filter($theme, "theme");
    Vars::set("theme_" . $page, $theme);
    return $theme;
}

function delete_custom_data(?string $var = null, ?string $page = null): void {
    if (!$page) {
        $page = get_requested_pagename();
    }
    $data = get_custom_data($page);
    if (is_null($data)) {
        $data = [];
    }
// Wenn $var gesetzt ist, nur $var aus custom_data löschen
    if ($var) {
        if (isset($data[$var])) {
            unset($data[$var]);
        }
    } // Wenn $var nicht gesetzt ist, alle Werte von custom_data löschen
    else {
        $data = [];
    }

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    db_query("UPDATE " . tbname("content") . " SET custom_data = '" . db_escape($json) . "' WHERE slug='" . db_escape($page) . "'");
}

function set_custom_data(?string$var, $value, ?string $page = null): void {
    if (!$page) {
        $page = get_requested_pagename();
    }
    $data = get_custom_data($page);
    if (is_null($data)) {
        $data = [];
    }

    $data[$var] = $value;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    db_query("UPDATE " . tbname("content") . " SET custom_data = '" . db_escape($json) . "' WHERE slug='" . db_escape($page) . "'");
}

function language_selection(): void {
    Template::languageSelection();
}

function get_category(): ?string {
    $current_page = get_page();
    if (!$current_page["category_id"]) {
        return null;
    }
    return Categories::getCategoryById(intval($current_page["category_id"]));
}

function category(): void {
    Template::escape(get_category());
}

function get_body_classes(): string {
    return Template::getBodyClasses();
}

function body_classes(): void {
    echo Template::getBodyClasses();
}

// Gibt "Diese Seite läuft mit UliCMS" aus
function poweredByUliCMS(): void {
    Template::poweredByUliCMS();
}

// Einen zufälligen Banner aus der Datenbank ausgeben
function random_banner(): void {
    Template::randomBanner();
}

function logo(): void {
    Template::logo();
}

function year($format = "Y"): void {
    Template::year($format);
}

function homepage_owner(): void {
    Template::homepageOwner();
}

function get_homepage_title(): string {
    $homepage_title = Settings::get("homepage_title_" . $_SESSION["language"]);
    if (!$homepage_title) {
        $homepage_title = Settings::get("homepage_title");
    }
    return _esc($homepage_title);
}

function homepage_title(): void {
    echo get_homepage_title();
}

function get_meta_keywords(): string {
    $ipage = db_escape($_GET["seite"]);
    $result = db_query("SELECT meta_keywords FROM " . tbname("content") . " WHERE slug='$ipage' AND language='" . db_escape($_SESSION["language"]) . "'");

    if (db_num_rows($result) > 0) {
        while ($row = db_fetch_object($result)) {
            if (StringHelper::isNotNullOrEmpty($row->meta_keywords)) {
                return $row->meta_keywords;
            }
        }
    }
    $meta_keywords = Settings::get("meta_keywords_" . $_SESSION["language"]);
    if (!$meta_keywords) {
        $meta_keywords = Settings::get("meta_keywords");
    }

    return $meta_keywords;
}

function meta_keywords(): void {
    $value = get_meta_keywords();
    if ($value) {
        echo $value;
    }
}

function get_meta_description(?string $ipage = null): string {
    $ipage = db_escape($_GET["seite"]);
    $result = db_query("SELECT meta_description FROM " . tbname("content") . " WHERE slug='$ipage' AND language='" . db_escape($_SESSION["language"]) . "'");
    if ($ipage == "") {
        $result = db_query("SELECT meta_description FROM " . tbname("content") . " ORDER BY id LIMIT 1", $connection);
    }
    if (db_num_rows($result) > 0) {
        while ($row = db_fetch_object($result)) {
            if (!empty($row->meta_description)) {
                return $row->meta_description;
            }
        }
    }
    $meta_description = Settings::get("meta_description_" . $_SESSION["language"]);
    if (!$meta_description) {
        $meta_description = Settings::get("meta_description");
    }

    return $meta_description;
}

function meta_description() {
    $value = get_meta_keywords();
    if ($value) {
        echo $value;
    }
}

function get_title(?string $ipage = null, bool$headline = false): string {
    $cacheVar = $headline ? "headline" : "title";
    if (Vars::get($cacheVar)) {
        return Vars::get($cacheVar);
    }

    $errorPage403 = intval(Settings::getLanguageSetting("error_page_403", getCurrentLanguage()));
    $errorPage404 = intval(Settings::getLanguageSetting("error_page_404", getCurrentLanguage()));

    if (is_404()) {
        if ($errorPage404) {
            $content = ContentFactory::getByID($errorPage404);
            if ($content->id !== null) {
                return $content->getHeadline();
            }
        }
        return get_translation("page_not_found");
    } else if (is_403()) {
        if ($errorPage403) {
            $content = ContentFactory::getByID($errorPage403);
            if ($content->id !== null) {
                return $content->getHeadline();
            }
        }
        return get_translation("forbidden");
    }

    $ipage = db_escape($_GET["seite"]);
    $result = db_query("SELECT alternate_title, title FROM " . tbname("content") . " WHERE slug='$ipage' AND language='" . db_escape($_SESSION["language"]) . "'", $connection);
    if ($ipage == "") {
        $result = db_query("SELECT title, alternate_title FROM " . tbname("content") . " ORDER BY id LIMIT 1");
    }
    if (db_num_rows($result) > 0) {
        while ($row = db_fetch_object($result)) {
            if ($headline and isset($row->alternate_title) and ! empty($row->alternate_title)) {
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
}

function title(?string $ipage = null): void {
    echo get_title($ipage);
}

function get_headline(?string $ipage = null): string {
    return get_title($ipage, true);
}

function headline(?string $ipage = null): void {
    echo get_headline($ipage);
}

function apply_filter($text, string $type) {
    $modules = getAllModules();
    $disabledModules = Vars::get("disabledModules");
    for ($i = 0; $i < count($modules); $i ++) {
        if (faster_in_array($modules[$i], $disabledModules)) {
            continue;
        }
        $module_content_filter_file1 = getModulePath($modules[$i], true) . $modules[$i] . "_" . $type . "_filter.php";
        $module_content_filter_file2 = getModulePath($modules[$i], true) . "filters/" . $type . ".php";

        $main_class = getModuleMeta($modules[$i], "main_class");
        $controller = null;
        if ($main_class) {
            $controller = ControllerRegistry::get($main_class);
        }
        $escapedName = ModuleHelper::underscoreToCamel($type . "_filter");
        if ($controller and method_exists($controller, $escapedName)) {
            $text = $controller->$escapedName($text);
        } else if (file_exists($module_content_filter_file1)) {
            require_once $module_content_filter_file1;
            if (function_exists($modules[$i] . "_" . $type . "_filter")) {
                $text = call_user_func($modules[$i] . "_" . $type . "_filter", $text);
            }
        } else if (file_exists($module_content_filter_file2)) {
            require_once $module_content_filter_file2;
            if (function_exists($modules[$i] . "_" . $type . "_filter")) {
                $text = call_user_func($modules[$i] . "_" . $type . "_filter", $text);
            }
        }
    }

    return $text;
}

function get_motto(): string {
    return Template::getMotto();
}

function motto(): void {
    echo Template::motto();
}

function get_frontpage(): string {
    setLanguageByDomain();
    if (isset($_SESSION["language"])) {
        $frontpage = Settings::get("frontpage_" . $_SESSION["language"]);
        if ($frontpage) {
            return $frontpage;
        }
    }
    return Settings::get("frontpage");
}

function get_requested_pagename(): string {
    $value = get_frontpage();

    if (StringHelper::isNotNullOrWhitespace($_GET["seite"])) {
        $value = $_GET["seite"];
    }
    return Database::escapeValue($value);
}

function set_requested_pagename(string $slug, ?string $language = null, ?string $format = "html"): void {
    if (!$language) {
        $language = getCurrentLanguage();
    }
    $_GET["seite"] = $slug;
    $_REQUEST["seite"] = $slug;

    $_GET["language"] = $language;
    $_REQUEST["language"] = $language;
    $_SESSION["language"] = $language;
    set_format($format);
}

function is_home(): bool {
    return get_requested_pagename() === get_frontpage();
}

function is_frontpage(): bool {
    return is_home();
}

function is_200(): bool {
    return check_status() == "200 OK";
}

function is_403(): bool {
    return check_status() == "403 Forbidden";
}

function is_404(): bool {
    return check_status() == "404 Not Found";
}

function is_500(): bool {
    return check_status() == "500 Internal Server Error";
}

function is_503(): bool {
    return check_status() == "503 Service Unavailable";
}

function buildtree(array $src_arr, ?int $parent_id = 0, ?array $tree = []): array {
    foreach ($src_arr as $idx => $row) {
        if ($row['parent'] == $parent_id) {
            foreach ($row as $k => $v) {
                $tree[$row['id']][$k] = $v;
            }
            unset($src_arr[$idx]);
            $tree[$row['id']]['children'] = buildtree($src_arr, $row['id']);
        }
    }
    ksort($tree);
    return $tree;
}

function parent_item_contains_current_page(?int $id): bool {
    $retval = false;
    if (!$id) {
        return $retval;
    }
    $id = intval($id);
    $language = $_SESSION["language"];
    $sql = "SELECT id, slug, parent_id FROM " . tbname("content") . " WHERE language = '$language' AND active = 1 AND `deleted_at` IS NULL";
    $r = db_query($sql);

    $data = [];
    while ($row = db_fetch_assoc($r)) {
        $data[] = $row;
    }

    $tree = buildtree($data, $id);
    foreach ($tree as $key) {
        if ($key["slug"] == get_requested_pagename()) {
            $retval = true;
        }
    }
    return $retval;
}

function get_menu(string $name = "top", ?int $parent_id = null, bool $recursive = true, string $order = "position") {
    $html = "";
    $name = db_escape($name);
    $language = $_SESSION["language"];
    $sql = "SELECT id, slug, access, redirection, title, alternate_title, menu_image, target, type, link_to_language, position FROM " . tbname("content") . " WHERE menu='$name' AND language = '$language' AND active = 1 AND `deleted_at` IS NULL AND hidden = 0 and type <> 'snippet' and parent_id ";

    if (is_null($parent_id)) {
        $sql .= " IS NULL ";
    } else {
        $sql .= " = " . intval($parent_id) . " ";
    }
    $sql .= " ORDER by " . $order;
    $result = db_query($sql);

    if (db_num_rows($result) == 0) {
        return $html;
    }

    if (is_null($parent_id)) {
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
            $containsCurrentItem = parent_item_contains_current_page(intval($row->id));

            $additional_classes = " menu-link-to-" . $row->id . " ";
            if ($containsCurrentItem) {
                $additional_classes .= "contains-current-page ";
            }

            if (get_requested_pagename() != $row->slug) {
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

            $redirection = $row->redirection;
            if ($row->type == "language_link" && !is_null($row->link_to_language)) {
                $language = new Language($row->link_to_language);
                $redirection = $language->getLanguageLink();
            }
// if content has type link or node url is the target url else build seo url
            $url = ($row->type == "link" or $row->type == "node") ? $row->redirection : buildSEOUrl($row->slug);
            $url = Template::getEscape($url);

            if (get_requested_pagename() != $row->slug) {
                $html .= "<a href='" . $url . "' target='" . $row->target . "' class='" . trim($additional_classes) . "'>";
            } else {
                $html .= "<a class='menu_active_link" . rtrim($additional_classes) . "' href='" . $url . "' target='" . $row->target . "'>";
            }
            if (!is_null($row->menu_image) and ! empty($row->menu_image)) {
                $html .= '<img src="' . $row->menu_image . '" alt="' . _esc($title) . '"/>';
            } else {
                $html .= _esc($title);
            }
            $html .= "</a>\n";

            if ($recursive) {
                $html .= get_menu($name, intval($row->id), true, $order);
            }

            $html .= "</li>";
        }
    }
    $html .= "</ul>";
    return $html;
}

function menu(string $name = "top", ?int $parent = null, bool $recursive = true, string $order = 'position'): void {
    echo get_menu($name, $parent, $recursive, $order);
}

function output_favicon_code(): void {
    echo get_output_favicon_code();
}

function get_output_favicon_code(): string {
    $url = "content/images/favicon.ico";
    if (defined("ULICMS_DATA_STORAGE_URL")) {
        $url = ULICMS_DATA_STORAGE_URL . "/" . $url;
    }
    $path = ULICMS_DATA_STORAGE_ROOT . "/content/images/favicon.ico";
    $html = "";
    if (file_exists($path)) {
        $url .= "?time=" . File::getLastChanged($path);
        $html = '<link rel="icon" href="' . $url . '" type="image/x-icon" />' . '<link rel="shortcut icon" href="' . $url . '" type="image/x-icon" />';
    }
    return $html;
}

function base_metas(): void {
    Template::baseMetas();
}

function get_base_metas(): void {
    Template::getBaseMetas();
}

function head(): void {
    base_metas();
}

function get_head(): string {
    return get_base_metas();
}

function author(): void {
    echo get_author();
}

function get_page(?string $slug = ''): ?array {
    if (empty($slug)) {
        $slug = $_GET["seite"];
    }
    if (empty($slug)) {
        $slug = get_frontpage();
    }
    if (Vars::get("page_" . $slug)) {
        return Vars::get("page_" . $slug);
    }
    $result = db_query("SELECT * FROM " . tbname("content") . " WHERE slug='" . db_escape($slug) . "' AND language='" . db_escape($_SESSION["language"]) . "'");
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_assoc($result);
        Vars::set("page_" . $slug, $dataset);
        return $dataset;
    } else {
        Vars::set("page_" . $slug, null);
        return null;
    }
}

function content(): void {
    Template::content();
}

function get_content(): string {
    return Template::getContent();
}

function checkforAccessForDevice(string $access): bool {
    $access = explode(",", $access);
    $allowed = false;
    if (faster_in_array("mobile", $access) and is_mobile()) {
        $allowed = true;
    }
    if (faster_in_array("desktop", $access) and ! is_mobile()) {
        $allowed = true;
    }
    if (!faster_in_array("mobile", $access) and ! faster_in_array("desktop", $access)) {
        $allowed = true;
    }
    return $allowed;
}

function checkAccess(string $access = ""): ?string {
    $access_for_device = checkforAccessForDevice($access);
    if (!$access_for_device) {
        return null;
    }
    $access = explode(",", $access);
    if (faster_in_array("all", $access)) {
        return "all";
    }
    if (faster_in_array("registered", $access) and is_logged_in()) {
        return "registered";
    }
    for ($i = 0; $i < count($access); $i ++) {
        if (is_numeric($access[$i]) and isset($_SESSION["group_id"]) and $access[$i] == $_SESSION["group_id"]) {
            return $access[$i];
        }
    }
    return null;
}

function check_status(): string {
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
    if ($_GET["seite"] == "") {
        $_GET["seite"] = get_frontpage();
    }

    $page = $_GET["seite"];

    if (!is_active() and ! is_logged_in()) {
        return "403 Forbidden";
    }

    $test = get_page($_GET["seite"]);
    if (!$test or ! is_null($test["deleted_at"])) {
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

DefaultContentTypes::initTypes();
