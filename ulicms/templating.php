<?php

function esc($value)
{
    Template::escape($value);
}

function _esc($value)
{
    return Template::getEscape($value);
}

function html5_doctype()
{
    echo get_html5_doctype();
}

function get_html5_doctype()
{
    $html = '<!doctype html>';
    $html .= "\r\n";
    return $html;
}

function og_html_prefix()
{
    echo get_og_html_prefix();
}

function get_og_html_prefix()
{
    $html = '<html prefix="og: http://ogp.me/ns#" lang="' . getCurrentLanguage() . '">';
    $html .= "\r\n";
    return $html;
}

function og_tags()
{
    echo get_og_tags();
}

function get_og_tags($systemname = null)
{
    $html = "";
    if (is_200()) {
        $og_data = get_og_data($systemname);
        $og_title = $og_data["og_title"];
        $og_type = $og_data["og_type"];
        $og_image = $og_data["og_image"];
        $og_description = $og_data["og_description"];
        $og_url = getCurrentURL();
        
        // Falls kein og_title für die Seite gesetzt ist, Standardtitel bzw. Headline verwenden
        if (is_null($og_title) or empty($og_title)) {
            $og_title = get_headline();
        }
        
        if (is_null($og_type) or empty($og_type)) {
            $og_type = Settings::get("og_type");
        }
        
        if (is_null($og_image) or empty($og_image)) {
            $og_image = Settings::get("og_image");
        }
        
        if (! $og_type) {
            $og_type = "article";
        }
        
        if (! empty($og_image) and ! startsWith($og_image, "http")) {
            $og_image = ModuleHelper::getBaseUrl() . ltrim($og_image, "/");
        }
        if (empty($og_image)) {
            $page = get_page($systemname);
            if ($page["type"] == "article" && ! StringHelper::isNullOrWhitespace($page["article_image"]));
            $og_image = ltrim($page["article_image"], "/");
        }
        if (! empty($og_image) and ! startsWith($og_image, "http")) {
            $og_image = ModuleHelper::getBaseUrl() . ltrim($og_image, "/");
        }
        if (is_null($og_description) or empty($og_description)) {
            $og_description = get_meta_description();
        }
        
        $og_title = apply_filter($og_title, "og_title");
        $og_type = apply_filter($og_type, "og_type");
        $og_url = apply_filter($og_url, "og_url");
        $og_image = apply_filter($og_image, "og_image");
        $og_description = apply_filter($og_description, "og_description");
        
        $html .= '<meta property="og:title" content="' . htmlspecialchars($og_title) . '" />';
        $html .= "\r\n";
        
        if (! is_null($og_description) and ! empty($og_description)) {
            $html .= '<meta property="og:description" content="' . htmlspecialchars($og_description) . '" />';
            $html .= "\r\n";
        }
        
        $html .= '<meta property="og:type" content="' . htmlspecialchars($og_type) . '" />';
        $html .= "\r\n";
        
        $html .= '<meta property="og:url" content="' . htmlspecialchars($og_url) . '" />';
        $html .= "\r\n";
        
        $html .= '<meta property="og:image" content="' . htmlspecialchars($og_image) . '" />';
        $html .= "\r\n";
        $html .= '<meta property="og:site_name" content="' . get_homepage_title() . '" />';
        $html .= "\r\n";
    }
    
    $html = apply_filter($html, "og_html");
    return $html;
}

function get_og_data($systemname = "")
{
    if (empty($systemname)) {
        $systemname = $_GET["seite"];
    }
    
    if (empty($systemname)) {
        $systemname = get_frontpage();
    }
    $query = db_query("SELECT og_title, og_type, og_image, og_description FROM " . tbname("content") . " WHERE systemname='" . db_escape($systemname) . "' AND language='" . db_escape($_SESSION["language"]) . "'");
    if (db_num_rows($query) > 0) {
        return db_fetch_assoc($query);
    } else {
        return null;
    }
}

function get_all_combined_html()
{
    $html = "";
    $html .= getCombinedStylesheetHtml();
    $html .= "\r\n";
    $html .= combinedScriptHtml();
    $html .= "\r\n";
    return $html;
}

function edit_button()
{
    echo get_edit_button();
}

function get_edit_button()
{
    $html = "";
    if (is_logged_in() and ! containsModule()) {
        $acl = new ACL();
        if ($acl->hasPermission("pages") and Flags::getNoCache() && is_200()) {
            $id = get_ID();
            $page = ContentFactory::getById($id);
            if (in_array($page->language, getAllLanguages(true))) {
                $html .= "<div class=\"ulicms_edit\">[<a href=\"admin/index.php?action=pages_edit&page=$id\">" . get_translation("edit") . "</a>]</div>";
            }
        }
    }
    return $html;
}

function all_combined_html()
{
    echo get_all_comined_html();
}

function get_ID()
{
    if (! is_null(Vars::get("id"))) {
        return Vars::get("id");
    }
    if (! $page) {
        $page = get_requested_pagename();
    }
    $result = null;
    $sql = "SELECT `id` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $query = db_query($sql);
    if (db_num_rows($query) > 0) {
        $result = db_fetch_object($query);
        $result = $result->id;
    }
    Vars::set("id", $result);
    return $result;
}

function is_active()
{
    if (! is_null(Vars::get("active"))) {
        return Vars::get("active");
    }
    if (! $page) {
        $page = get_requested_pagename();
    }
    $result = 1;
    $sql = "SELECT `active` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $query = db_query($sql);
    if (db_num_rows($query) > 0) {
        $result = db_fetch_object($query);
        $result = $result->active;
    }
    Vars::set("active", $result);
    return $result;
}

function get_type()
{
    if (Vars::get("type")) {
        return Vars::get("type");
    }
    if (! $page) {
        $page = get_requested_pagename();
    }
    $result = "";
    $sql = "SELECT `type` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $query = db_query($sql);
    if (db_num_rows($query) > 0) {
        $result = db_fetch_object($query);
        $result = $result->type;
    }
    if (empty($result)) {
        $result = "page";
    }
    $result = apply_filter($result, "get_type");
    Vars::set("type", $result);
    return $result;
}

function get_article_meta($page = null)
{
    if (! $page) {
        $page = get_requested_pagename();
    }
    $result = null;
    $sql = "SELECT `article_author_name`, `article_author_email`, CASE WHEN `article_date` is not null then UNIX_TIMESTAMP(article_date) else null end as article_date, `article_image`, `excerpt` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . Database::escapeValue($_SESSION["language"]) . "'";
    $query = db_query($sql);
    if (db_num_rows($query) > 0) {
        $result = Database::fetchObject($query);
        $result->excerpt = replaceShortcodesWithModules($result->excerpt);
    }
    $result = apply_filter($result, "get_article_meta");
    return $result;
}

function get_cache_control()
{
    if (! is_null(Vars::get("cache_control"))) {
        return Vars::get("cache_control");
    }
    if (! $page) {
        $page = get_requested_pagename();
    }
    $result = "";
    $sql = "SELECT `cache_control` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $query = db_query($sql);
    if ($query and db_num_rows($query) > 0) {
        $result = db_fetch_object($query);
        $result = $result->cache_control;
    }
    if (empty($result)) {
        $result = "auto";
    }
    $result = apply_filter($result, "get_cache_control");
    Vars::set("cache_control", $result);
    return $result;
}

function get_text_position()
{
    if (! $page) {
        $page = get_requested_pagename();
    }
    $result = "";
    $sql = "SELECT `text_position` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $query = db_query($sql);
    if (db_num_rows($query) > 0) {
        $result = db_fetch_object($query);
        $result = $result->text_position;
    }
    if (empty($result)) {
        $result = "before";
    }
    return $result;
}

function get_category_id($page = null)
{
    if (! $page) {
        $page = get_requested_pagename();
    }
    $result = null;
    $sql = "SELECT `category` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $query = db_query($sql);
    if (db_num_rows($query) > 0) {
        $result = db_fetch_object($query);
        $result = $result->category;
    }
    return $result;
}

function category_id($page = null)
{
    echo get_category_id($page);
}

function get_parent($page = null)
{
    if (! $page) {
        $page = get_requested_pagename();
    }
    $result = "";
    $sql = "SELECT `parent` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $query = db_query($sql);
    if (db_num_rows($query) > 0) {
        $result = db_fetch_object($query);
        $result = $result->parent;
    }
    if (empty($result)) {
        $result = null;
    }
    return $result;
}

function get_custom_data($page = null)
{
    if (! $page) {
        $page = get_requested_pagename();
    }
    
    $sql = "SELECT `custom_data` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $query = db_query($sql);
    if (db_num_rows($query) > 0) {
        $result = db_fetch_object($query);
        return json_decode($result->custom_data, true);
    }
    return null;
}

function include_jquery()
{
    if (Settings::get("disable_auto_include_jquery")) {
        return;
    }
    $disabled_on_pages = Settings::get("jquery_disabled_on");
    if ($disabled_on_pages) {
        $disabled_on_pages = trim($disabled_on_pages);
        $disabled_on_pages = explode(";", $disabled_on_pages);
    } else {
        $disabled_on_pages = array();
    }
    
    if (! faster_in_array(get_requested_pagename(), $disabled_on_pages)) {
        ?>
<script type="text/javascript" src="<?php echo get_jquery_url();?>"></script>
<?php
        do_event("after_jquery_include");
    }
}

function get_access($page = null)
{
    if (! $page) {
        $page = get_requested_pagename();
    }
    $sql = "SELECT `access` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
    $query = db_query($sql);
    if (db_num_rows($query) > 0) {
        $result = db_fetch_object($query);
        $access = explode(",", $result->access);
        return $access;
    }
    return null;
}

function get_redirection($page = null)
{
    if (! $page) {
        $page = get_requested_pagename();
    }
    $sql = "SELECT `redirection` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "' and type='link'";
    $query = db_query($sql);
    if (db_num_rows($query) > 0) {
        $result = db_fetch_object($query);
        if (! empty($result->redirection) and ! is_null($result->redirection)) {
            return $result->redirection;
        }
        return null;
    }
    return null;
}

function get_theme($page = null)
{
    if (! $page) {
        $page = get_requested_pagename();
    }
    
    if (! is_null(Vars::get("theme_" . $page))) {
        return Vars::get("theme_" . $page);
    }
    $theme = Settings::get("theme");
    $mobile_theme = Settings::get("mobile_theme");
    if ($mobile_theme and ! empty($mobile_theme) and is_mobile()) {
        $theme = $mobile_theme;
    }
    
    if (is_200()) {
        $sql = "SELECT `theme` FROM " . tbname("content") . " WHERE systemname='" . db_escape($page) . "'  AND language='" . db_escape($_SESSION["language"]) . "'";
        $query = db_query($sql);
        if ($query and db_num_rows($query) > 0) {
            $data = db_fetch_object($query);
            if (isset($data->theme) and ! empty($data->theme) and ! is_null($data->theme)) {
                $theme = $data->theme;
            }
        }
    }
    $theme = apply_filter($theme, "theme");
    Vars::set("theme_" . $page, $theme);
    return $theme;
}

function delete_custom_data($var = null, $page = null)
{
    if (! $page) {
        $page = get_requested_pagename();
    }
    $data = get_custom_data($page);
    if (is_null($data)) {
        $data = array();
    }
    // Wenn $var gesetzt ist, nur $var aus custom_data löschen
    if ($var) {
        if (isset($data[$var])) {
            unset($data[$var]);
        }
    } // Wenn $var nicht gesetzt ist, alle Werte von custom_data löschen
    else {
        $data = array();
    }
    
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    return db_query("UPDATE " . tbname("content") . " SET custom_data = '" . db_escape($json) . "' WHERE systemname='" . db_escape($page) . "'");
}

function set_custom_data($var, $value, $page = null)
{
    if (! $page) {
        $page = get_requested_pagename();
    }
    $data = get_custom_data($page);
    if (is_null($data)) {
        $data = array();
    }
    
    $data[$var] = $value;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    return db_query("UPDATE " . tbname("content") . " SET custom_data = '" . db_escape($json) . "' WHERE systemname='" . db_escape($page) . "'");
}

function language_selection()
{
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

function get_category()
{
    $current_page = get_page();
    return Categories::getCategoryById($current_page["category"]);
}

function category()
{
    Template::escapape(get_category());
}

function get_body_classes()
{
    $str = "page-id-" . get_ID() . " ";
    if (is_frontpage()) {
        $str .= "home ";
    }
    
    if (is_404()) {
        $str .= "error404 ";
    }
    
    if (is_403()) {
        $str .= "error403 ";
    }
    
    if (is_404() or is_403()) {
        $str .= "errorPage ";
    } else {
        $str .= "page ";
    }
    
    if (is_mobile()) {
        $str .= "mobile ";
    } else {
        $str .= "desktop ";
    }
    
    if (containsModule(get_requested_pagename())) {
        $str .= "containsModule ";
    }
    $str = trim($str);
    $str = apply_filter($str, "body_classes");
    return $str;
}

function body_classes()
{
    echo get_body_classes();
}

// Gibt "Diese Seite läuft mit UliCMS" aus
function poweredByUliCMS()
{
    translation("POWERED_BY_ULICMS");
}

// Einen zufälligen Banner aus der Datenbank ausgeben
function random_banner()
{
    Template::randomBanner();
}

function logo()
{
    Template::logo();
}

function year($format = "Y")
{
    Template::year($format);
}

function homepage_owner()
{
    echo Settings::get("homepage_owner");
}

function get_homepage_title()
{
    $homepage_title = Settings::get("homepage_title_" . $_SESSION["language"]);
    if (! $homepage_title) {
        $homepage_title = Settings::get("homepage_title");
    }
    return htmlspecialchars($homepage_title, ENT_QUOTES, "UTF-8");
}

function homepage_title()
{
    echo get_homepage_title();
}
$status = check_status();

function get_meta_keywords($dummy = null)
{
    $ipage = db_escape($_GET["seite"]);
    $query = db_query("SELECT meta_keywords FROM " . tbname("content") . " WHERE systemname='$ipage' AND language='" . db_escape($_SESSION["language"]) . "'");
    
    if (db_num_rows($query) > 0) {
        while ($row = db_fetch_object($query)) {
            if (StringHelper::isNotNullOrEmpty($row->meta_keywords)) {
                return $row->meta_keywords;
            }
        }
    }
    $meta_keywords = Settings::get("meta_keywords_" . $_SESSION["language"]);
    if (! $meta_keywords) {
        $meta_keywords = Settings::get("meta_keywords");
    }
    
    return $meta_keywords;
}

function meta_keywords($dummy = null)
{
    $value = get_meta_keywords($dummy);
    if ($value) {
        echo $value;
    }
}

function get_meta_description($ipage = null)
{
    $ipage = db_escape($_GET["seite"]);
    $query = db_query("SELECT meta_description FROM " . tbname("content") . " WHERE systemname='$ipage' AND language='" . db_escape($_SESSION["language"]) . "'");
    if ($ipage == "") {
        $query = db_query("SELECT meta_description FROM " . tbname("content") . " ORDER BY id LIMIT 1", $connection);
    }
    if (db_num_rows($query) > 0) {
        while ($row = db_fetch_object($query)) {
            if (! empty($row->meta_description)) {
                return $row->meta_description;
            }
        }
    }
    $meta_description = Settings::get("meta_description_" . $_SESSION["language"]);
    if (! $meta_description) {
        $meta_description = Settings::get("meta_description");
    }
    
    return $meta_description;
}

function meta_description($dummy = null)
{
    $value = get_meta_keywords($dummy);
    if ($value) {
        echo $value;
    }
}

function get_title($ipage = null, $headline = false)
{
    $cacheVar = $headline ? "headline" : "title";
    if (Vars::get($cacheVar)) {
        return Vars::get($cacheVar);
    }
    $status = check_status();
    if ($status == "404 Not Found") {
        return get_translation("page_not_found");
    } else if ($status == "403 Forbidden") {
        return get_translation("forbidden");
    }
    
    $ipage = db_escape($_GET["seite"]);
    $query = db_query("SELECT alternate_title, title FROM " . tbname("content") . " WHERE systemname='$ipage' AND language='" . db_escape($_SESSION["language"]) . "'", $connection);
    if ($ipage == "") {
        $query = db_query("SELECT title, alternate_title FROM " . tbname("content") . " ORDER BY id LIMIT 1");
    }
    if (db_num_rows($query) > 0) {
        while ($row = db_fetch_object($query)) {
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

function title($ipage = null)
{
    echo get_title($ipage);
}

function get_headline($ipage = null)
{
    return get_title($ipage, true);
}

function headline($ipage = null)
{
    echo get_headline($ipage);
}

function import($ipage)
{
    $ipage = db_escape($ipage);
    if ($ipage == "") {
        $query = db_query("SELECT content FROM " . tbname("content") . " WHERE language='" . db_escape($_SESSION["language"]) . "' ORDER BY id LIMIT 1");
    } else {
        $query = db_query("SELECT content FROM " . tbname("content") . " WHERE systemname='$ipage' AND language='" . db_escape($_SESSION["language"]) . "'");
    }
    if (db_num_rows($query) == 0) {
        return false;
    } else {
        while ($row = db_fetch_object($query)) {
            $data = CustomData::get();
            if (is_false($data["disable_shortcodes"])) {
                $row->content = replaceShortcodesWithModules($row->content);
                $row->content = apply_filter($row->content, "content");
            }
            echo $row->content;
            return true;
        }
    }
}

function apply_filter($text, $type)
{
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
        } else if (is_file($module_content_filter_file1)) {
            include_once $module_content_filter_file1;
            if (function_exists($modules[$i] . "_" . $type . "_filter")) {
                $text = call_user_func($modules[$i] . "_" . $type . "_filter", $text);
            }
        } else if (is_file($module_content_filter_file2)) {
            include_once $module_content_filter_file2;
            if (function_exists($modules[$i] . "_" . $type . "_filter")) {
                $text = call_user_func($modules[$i] . "_" . $type . "_filter", $text);
            }
        }
    }
    
    return $text;
}

function get_motto()
{
    // Existiert ein Motto für diese Sprache? z.B. motto_en
    $motto = Settings::get("motto_" . $_SESSION["language"]);
    
    // Ansonsten Standard Motto
    if (! $motto) {
        $motto = Settings::get("motto");
    }
    return htmlspecialchars($motto, ENT_QUOTES, "UTF-8");
}

function motto()
{
    echo get_motto();
}

function get_frontpage()
{
    setLanguageByDomain();
    if (isset($_SESSION["language"])) {
        $frontpage = Settings::get("frontpage_" . $_SESSION["language"]);
        if ($frontpage) {
            return $frontpage;
        }
    }
    return Settings::get("frontpage");
}

function get_requested_pagename()
{
    $value = db_escape($_GET["seite"]);
    if ($value == "") {
        $value = get_frontpage();
    }
    return $value;
}

function is_home()
{
    return get_requested_pagename() === get_frontpage();
}

function is_frontpage()
{
    return is_home();
}

function is_200()
{
    return check_status() == "200 OK";
}

function is_403()
{
    return check_status() == "403 Forbidden";
}

function is_404()
{
    return check_status() == "404 Not Found";
}

function is_500()
{
    return check_status() == "500 Internal Server Error";
}

function is_503()
{
    return check_status() == "503 Service Unavailable";
}

function buildtree($src_arr, $parent_id = 0, $tree = array())
{
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

function parent_item_contains_current_page($id)
{
    $retval = false;
    $id = intval($id);
    $language = $_SESSION["language"];
    $sql = "SELECT id, systemname, parent FROM " . tbname("content") . " WHERE language = '$language' AND active = 1 AND `deleted_at` IS NULL";
    $r = db_query($sql);
    
    $data = array();
    while ($row = db_fetch_assoc($r)) {
        $data[] = $row;
    }
    
    $tree = buildtree($data, $id);
    foreach ($tree as $key) {
        if ($key["systemname"] == get_requested_pagename()) {
            $retval = true;
        }
    }
    return $retval;
}

function get_menu($name = "top", $parent = null, $recursive = true, $order = "position")
{
    $html = "";
    $name = db_escape($name);
    $language = $_SESSION["language"];
    $sql = "SELECT id, systemname, access, redirection, title, alternate_title, menu_image, target, type, link_to_language, position FROM " . tbname("content") . " WHERE menu='$name' AND language = '$language' AND active = 1 AND `deleted_at` IS NULL AND hidden = 0 and type <> 'snippet' and parent ";
    
    if (is_null($parent)) {
        $sql .= " IS NULL ";
    } else {
        $sql .= " = " . intval($parent) . " ";
    }
    $sql .= " ORDER by " . $order;
    $query = db_query($sql);
    
    if (db_num_rows($query) == 0) {
        return $html;
    }
    
    if (is_null($parent)) {
        $html .= "<ul class='menu_" . $name . " navmenu'>\n";
    } else {
        $containsCurrentItem = parent_item_contains_current_page($parent);
        
        $classes = "sub_menu";
        
        if ($containsCurrentItem) {
            $classes .= " contains-current-page";
        }
        $html .= "<ul class='" . $classes . "'>\n";
    }
    
    while ($row = db_fetch_object($query)) {
        if (checkAccess($row->access)) {
            $containsCurrentItem = parent_item_contains_current_page($row->id);
            
            $additional_classes = " menu-link-to-" . $row->id . " ";
            if ($containsCurrentItem) {
                $additional_classes .= "contains-current-page ";
            }
            
            if (get_requested_pagename() != $row->systemname) {
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
            if ($row->type == "language_link" && ! is_null($row->link_to_language)) {
                $language = new Language($row->link_to_language);
                $redirection = $language->getLanguageLink();
            }
            // if content has type link or node url is the target url else build seo url
            $url = ($row->type == "link" or $row->type == "node") ? $row->redirection : buildSEOUrl($row->systemname);
            $url = Template::getEscape($url);
            
            if (get_requested_pagename() != $row->systemname) {
                $html .= "<a href='" . $url . "' target='" . $row->target . "' class='" . trim($additional_classes) . "'>";
            } else {
                $html .= "<a class='menu_active_link" . rtrim($additional_classes) . "' href='" . $url . "' target='" . $row->target . "'>";
            }
            if (! is_null($row->menu_image) and ! empty($row->menu_image)) {
                $html .= '<img src="' . $row->menu_image . '" alt="' . htmlentities($title, ENT_QUOTES, "UTF-8") . '"/>';
            } else {
                $html .= htmlentities($title, ENT_QUOTES, "UTF-8");
            }
            $html .= "</a>\n";
            
            if ($recursive) {
                $html .= get_menu($name, $row->id, true, $order);
            }
            
            $html .= "</li>";
        }
    }
    $html .= "</ul>";
    return $html;
}

function menu($name = "top", $parent = null, $recursive = true, $order = 'position')
{
    echo get_menu($name, $parent, $recursive, $order);
}

function get_base_metas()
{
    ob_start();
    base_metas();
    return ob_get_clean();
}

function output_favicon_code()
{
    echo get_output_favicon_code();
}

function get_output_favicon_code()
{
    $url = "content/images/favicon.ico";
    if (defined("ULICMS_DATA_STORAGE_URL")) {
        $url = ULICMS_DATA_STORAGE_URL . "/" . $url;
    }
    $path = ULICMS_DATA_STORAGE_ROOT . "/content/images/favicon.ico";
    $html = "";
    if (is_file($path)) {
        $url .= "?time=" . File::getLastChanged($path);
        $html = '<link rel="icon" href="' . $url . '" type="image/x-icon" />' . "\r\n" . '<link rel="shortcut icon" href="' . $url . '" type="image/x-icon" />';
    }
    return $html;
}

function base_metas()
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
        $coreCssFile = ULICMS_ROOT . "/core.min.css";
        
        $coreCssFileURL = basename($coreCssFile) . "?time=" . File::getLastChanged($coreCssFile);
        
        echo '<link rel="stylesheet" type="text/css" href="' . $coreCssFileURL . '"/>';
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
        $font = Settings::get("default-font");
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

function head()
{
    base_metas();
}

function get_head()
{
    return get_base_metas();
}

function autor()
{
    echo get_autor();
}

function get_autor()
{
    $seite = $_GET["seite"];
    if (empty($seite)) {
        $query = db_query("SELECT systemname FROM " . tbname("content") . " ORDER BY id LIMIT 1");
        $result = db_fetch_object($query);
        $seite = $result->systemname;
    }
    
    if (check_status() != "200 OK") {
        return;
    }
    
    $query = db_query("SELECT systemname, autor FROM " . tbname("content") . " WHERE systemname='" . db_escape($seite) . "' AND language='" . db_escape($_SESSION["language"]) . "'", $connection);
    if (db_num_rows($query) < 1) {
        return;
    }
    $result = db_fetch_assoc($query);
    if ($result["systemname"] == "kontakt" || $result["systemname"] == "impressum" || StartsWith($result["systemname"], "menu_")) {
        return;
    }
    $query2 = db_query("SELECT firstname, lastname, username FROM " . tbname("users") . " WHERE id=" . $result["autor"], $connection);
    $result2 = db_fetch_array($query2);
    if (db_num_rows($query2) == 0) {
        return;
    }
    $datum = $result["created"];
    $out = Settings::get("autor_text");
    $out = str_replace("Vorname", $result2["firstname"], $out);
    $out = str_replace("Nachname", $result2["lastname"], $out);
    $out = str_replace("Username", $result2["username"], $out);
    if (! is_403() and ! is_404()) {
        return $out;
    }
}

function get_page($systemname = '')
{
    if (empty($systemname)) {
        $systemname = $_GET["seite"];
    }
    if (empty($systemname)) {
        $systemname = get_frontpage();
    }
    if (Vars::get("page_" . $systemname)) {
        return Vars::get("page_" . $systemname);
    }
    $query = db_query("SELECT * FROM " . tbname("content") . " WHERE systemname='" . db_escape($systemname) . "' AND language='" . db_escape($_SESSION["language"]) . "'");
    if (db_num_rows($query) > 0) {
        $result = db_fetch_assoc($query);
        Vars::set("page_" . $systemname, $result);
        return $result;
    } else {
        Vars::set("page_" . $systemname, null);
        return null;
    }
}

function content()
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

function get_content()
{
    ob_start();
    content();
    return ob_get_clean();
}

function checkforAccessForDevice($access)
{
    $access = explode(",", $access);
    $allowed = false;
    if (faster_in_array("mobile", $access) and is_mobile()) {
        $allowed = true;
    }
    if (faster_in_array("desktop", $access) and ! is_mobile()) {
        $allowed = true;
    }
    if (! faster_in_array("mobile", $access) and ! faster_in_array("desktop", $access)) {
        $allowed = true;
    }
    return $allowed;
}

function checkAccess($access = "")
{
    $access_for_device = checkforAccessForDevice($access);
    if (! $access_for_device) {
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

function check_status()
{
    $status = apply_filter("", "status");
    if (! empty($status)) {
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
    
    if (! is_active() and ! is_logged_in()) {
        return "403 Forbidden";
    }
    
    $test = get_page($_GET["seite"]);
    if (! $test or ! is_null($test["deleted_at"])) {
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

// load default content types;
DefaultContentTypes::initTypes();
