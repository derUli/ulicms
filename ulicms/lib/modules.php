<?php

declare(strict_types=1);

use UliCMS\Constants\ModuleEventConstants;

function getModuleMeta($module, $attrib = null) {
    $metadata_file = ModuleHelper::buildModuleRessourcePath(
                    $module,
                    "metadata.json",
                    true
    );
    if (!file_exists($metadata_file) or is_dir($metadata_file)) {
        return null;
    }

    $data = file_get_contents($metadata_file);
    $json = json_decode($data, true);

    if ($attrib && !isset($json[$attrib])) {
        return null;
    }
    return $attrib ? $json[$attrib] : $json;
}

// DEPRECATED:
// This function may be removed in future releases of UliCMS
// Use do_event()
function add_hook(
        string $name,
        string $runs = ModuleEventConstants::RUNS_ONCE
): void {
    trigger_error("add_hook() is deprecated. Please use do_event().",
            E_USER_DEPRECATED);
    do_event($name, $runs);
}

function do_event(
        string $name,
        string $runs = ModuleEventConstants::RUNS_ONCE
): void {
    $modules = getAllModules();
    $disabledModules = Vars::get("disabledModules");
    for ($hook_i = 0; $hook_i < count($modules); $hook_i ++) {
        if (faster_in_array($modules[$hook_i], $disabledModules)) {
            continue;
        }
        $file1 = getModulePath($modules[$hook_i], true) .
                $modules[$hook_i] . "_" . $name . ".php";
        $file2 = getModulePath($modules[$hook_i], true) .
                "hooks/" . $name . ".php";
        $main_class = getModuleMeta($modules[$hook_i], "main_class");
        $controller = null;
        if ($main_class) {
            $controller = ControllerRegistry::get($main_class);
        }
        ob_start();
        $escapedName = ModuleHelper::underscoreToCamel($name);
        if ($controller and method_exists($controller, $escapedName)) {
            echo $controller->$escapedName();
        } else if (file_exists($file1)) {
            if ($runs === ModuleEventConstants::RUNS_MULTIPLE) {
                require $file1;
            } else {
                require_once $file1;
            }
        } else if (file_exists($file2)) {

            if ($runs === ModuleEventConstants::RUNS_MULTIPLE) {
                require $file1;
            } else {
                require_once $file2;
            }
        }
        echo optimizeHtml(ob_get_clean());
    }
}

function stringContainsShortCodes(string $content, ?string $module = null): bool {
    $quot = '(' . preg_quote('&quot;') . ')?';
    return boolval(
            $module ?
            preg_match('/\[module=\"?' . $quot . preg_quote($module) . '\"?' .
                    $quot . '\]/m', $content) :
            preg_match('/\[module=\"?' . $quot . '([a-zA-Z0-9_]+)\"?' .
                    $quot . '\]/m', $content)
    );
}

// replace Shortcodes with modules
function replaceShortcodesWithModules(
        string $string,
        bool $replaceOther = true
): string {
    $string = $replaceOther ? replaceOtherShortCodes($string) : $string;

    $allModules = ModuleHelper::getAllEmbedModules();
    $disabledModules = Vars::get("disabledModules");

    foreach ($allModules as $module) {
        if (faster_in_array($module, $disabledModules)
                or ! stringContainsShortCodes($string, $module)) {
            continue;
        }
        $stringToReplace1 = '[module="' . $module . '"]';
        $stringToReplace2 = '[module=&quot;' . $module . '&quot;]';
        $stringToReplace3 = '[module=' . $module . ']';

        $module_mainfile_path = getModuleMainFilePath($module);
        $module_mainfile_path2 = getModuleMainFilePath2($module);

        if (file_exists($module_mainfile_path)) {
            require_once $module_mainfile_path;
        } else if (file_exists($module_mainfile_path2)) {
            require_once $module_mainfile_path2;
        }

        $main_class = getModuleMeta($module, "main_class");
        $controller = null;
        if ($main_class) {
            $controller = ControllerRegistry::get($main_class);
        }
        if ($controller and method_exists($controller, "render")) {
            $html_output = $controller->render();
        } else if (function_exists($module . "_render")) {
            $html_output = call_user_func($module . "_render");
        } else {
            throw new BadMethodCallException("Module $module "
                    . "has no render() method");
        }

        $string = str_replace($stringToReplace1, $html_output, $string);
        $string = str_replace($stringToReplace2, $html_output, $string);

        $string = str_replace($stringToReplace3, $html_output, $string);
        $string = str_replace('[title]', get_title(), $string);
    }
    $string = replaceVideoTags($string);
    $string = replaceAudioTags($string);

    $string = optimizeHtml($string);
    return $string;
}

function replaceOtherShortCodes(string $string): string {
    $string = str_ireplace('[title]', get_title(), $string);
    ob_start();
    logo();
    $string = str_ireplace('[logo]', ob_get_clean(), $string);
    $language = getCurrentLanguage(true);
    $checkbox = new PrivacyCheckbox($language);
    $string = str_ireplace(
            "[accept_privacy_policy]",
            $checkbox->render(),
            $string
    );
    ob_start();
    site_slogan();
    $string = str_ireplace('[motto]', ob_get_clean(), $string);
    ob_start();
    site_slogan();
    $string = str_ireplace('[slogan]', ob_get_clean(), $string);

    $string = str_ireplace('[category]', get_category(), $string);

    $token = get_csrf_token_html() .
            '<input type="url" name="my_homepage_url" '
            . 'class="antispam_honeypot" value="" autocomplete="nope">';
    $string = str_ireplace('[csrf_token_html]', $token, $string);

    // [tel] Links for tel Tags
    $string = preg_replace(
            '/\[tel\]([^\[\]]+)\[\/tel\]/i',
            '<a href="tel:$1" class="tel">$1</a>',
            $string
    );
    $string = preg_replace(
            '/\[skype\]([^\[\]]+)\[\/skype\]/i',
            '<a href="skye:$1?call" class="skype">$1</a>',
            $string
    );

    $string = str_ireplace("[year]", Template::getYear(), $string);
    $string = str_ireplace(
            "[homepage_owner]",
            Template::getHomepageOwner(),
            $string
    );

    preg_match_all("/\[include=([0-9]+)]/i", $string, $match);

    if (count($match) > 0) {
        for ($i = 0; $i < count($match[0]); $i ++) {
            $placeholder = $match[0][$i];
            $id = unhtmlspecialchars($match[1][$i]);
            $id = intval($id);

            $page = ContentFactory::getByID($id);
            // a page should not include itself
            // because that would cause an endless loop
            if ($page and $id != get_ID()) {
                $content = "";
                if ($page->active and checkAccess($page->access)) {
                    $content = $page->content;
                }
                $string = str_ireplace($placeholder, $content, $string);
            }
        }
    }
    return $string;
}

// Check if site contains a module
function containsModule(?string $page = null, ?string $module = null): bool {
    if (is_null($page)) {
        $page = get_requested_pagename();
    }

    if (!is_null(Vars::get("page_" . $page . "_contains_" . $module))) {
        return Vars::get("page_" . $page . "_contains_" . $module);
    }

    $result = db_query("SELECT content, module, `type` FROM " .
            tbname("content") . " WHERE slug = '" . db_escape($page) . "'");
    
    if(!Database::any($result)){
        return false;
    }
    
    $dataset = db_fetch_assoc($result);
    $content = $dataset["content"];
    $content = str_replace("&quot;", "\"", $content);
    if (!is_null($dataset["module"]) && !empty($dataset["module"])
            and $dataset["type"] == "module") {
        if (!$module or ( $module and $dataset["module"] == $module)) {
            Vars::set("page_" . $page . "_contains_" . $module, true);
            return true;
        }
    } else {
        $match = stringContainsShortCodes($content, $module);
        Vars::set("page_" . $page . "_contains_" . $module, $match);
        return $match;
    }
    Vars::set("page_" . $page . "_contains_" . $module, false);
    return false;
}
