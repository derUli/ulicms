<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Constants\ModuleEvent;
use App\Packages\ModuleManager;
use App\Security\PrivacyCheckbox;

function getModuleMeta($module, $attrib = null) {
    $metadata_file = \App\Helpers\ModuleHelper::buildModuleRessourcePath(
        $module,
        'metadata.json',
        true
    );
    if (! is_file($metadata_file)) {
        return null;
    }

    $data = file_get_contents($metadata_file);
    $json = json_decode($data, true);

    if ($attrib && ! isset($json[$attrib])) {
        return null;
    }
    return $attrib ? $json[$attrib] : $json;
}

function apply_filter($text, string $type) {
    $manager = new ModuleManager();
    $modules = $manager->getEnabledModuleNames();

    foreach($modules as $module) {
        $module_content_filter_file1 = getModulePath($module, true)
                . $module . '_' . $type . '_filter.php';
        $module_content_filter_file2 = getModulePath($module, true)
                . 'filters/' . $type . '.php';

        $main_class = getModuleMeta($module, 'main_class');
        $controller = null;
        if ($main_class) {
            $controller = ControllerRegistry::get($main_class);
        }

        $escapedName = \App\Helpers\ModuleHelper::underscoreToCamel($type . '_filter');
        if ($controller && method_exists($controller, $escapedName)) {
            $text = $controller->{$escapedName}($text);
        } elseif (is_file($module_content_filter_file1)) {
            require_once $module_content_filter_file1;
            if (function_exists($module . '_' . $type . '_filter')) {
                $text = call_user_func($module . '_' . $type .
                        '_filter', $text);
            }

        } elseif (is_file($module_content_filter_file2)) {
            require_once $module_content_filter_file2;
            if (function_exists($module . '_' . $type . '_filter')) {
                $text = call_user_func($module . '_' . $type .
                        '_filter', $text);
            }
        }
    }

    return $text;
}

function do_event(
    string $name,
    string $runs = ModuleEvent::RUNS_ONCE
): void {
    $manager = new \App\Packages\ModuleManager();
    $modules = $manager->getEnabledModuleNames();

    foreach($modules as $module) {
        $file1 = getModulePath($module, true) .
                $module . '_' . $name . '.php';
        $file2 = getModulePath($module, true) .
                'hooks/' . $name . '.php';
        $main_class = getModuleMeta($module, 'main_class');
        $controller = null;

        if ($main_class) {
            $controller = ControllerRegistry::get($main_class);
        }

        ob_start();
        $escapedName = \App\Helpers\ModuleHelper::underscoreToCamel($name);
        if ($controller && method_exists($controller, $escapedName)) {
            echo $controller->{$escapedName}();
        } elseif (is_file($file1)) {
            if ($runs === ModuleEvent::RUNS_MULTIPLE) {
                require $file1;
            } else {
                require_once $file1;
            }
        } elseif (is_file($file2)) {
            if ($runs === ModuleEvent::RUNS_MULTIPLE) {
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
    return (bool)(
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
): string {
    $manager = new \App\Packages\ModuleManager();
    $modules = $manager->getEnabledModuleNames();

    foreach ($modules as $module) {
        $stringToReplace1 = '[module="' . $module . '"]';
        $stringToReplace2 = '[module=&quot;' . $module . '&quot;]';
        $stringToReplace3 = '[module=' . $module . ']';

        $module_mainfile_path = getModuleMainFilePath($module);
        $module_mainfile_path2 = getModuleMainFilePath2($module);

        if (is_file($module_mainfile_path)) {
            require_once $module_mainfile_path;
        } elseif (is_file($module_mainfile_path2)) {
            require_once $module_mainfile_path2;
        }

        $main_class = getModuleMeta($module, 'main_class');
        $controller = $main_class ? ControllerRegistry::get($main_class) : null;

        $html_output = '';

        if ($controller && method_exists($controller, 'render')) {
            $html_output = $controller->render();
        } elseif (function_exists($module . '_render')) {
            $html_output = call_user_func($module . '_render');
        }

        $string = str_replace($stringToReplace1, $html_output, $string);
        $string = str_replace($stringToReplace2, $html_output, $string);

        $string = str_replace($stringToReplace3, $html_output, $string);
        $string = str_replace('[title]', get_title(), $string);
    }

    $string = replaceOtherShortCodes($string);
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
        '[accept_privacy_policy]',
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

    $string = str_ireplace('[year]', Template::getYear(), $string);
    $string = str_ireplace(
        '[homepage_owner]',
        Template::getHomepageOwner(),
        $string
    );

    preg_match_all("/\[include=([0-9]+)]/i", $string, $match);

    if (count($match) > 0) {
        $matchCount = count($match[0]);
        for ($i = 0; $i < $matchCount; $i++) {
            $placeholder = $match[0][$i];
            $id = _unesc($match[1][$i]);
            $id = (int)$id;

            $page = ContentFactory::getByID($id);
            // a page should not include itself
            // because that would cause an endless loop
            if ($page && $id != get_ID()) {
                $content = '';
                if ($page->active && checkAccess($page->access)) {
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
    $containsModule = false;

    if ($page === null) {
        $page = get_slug();
    }

    if (\App\Storages\Vars::get('page_' . $page . '_contains_' . $module) !== null) {
        return \App\Storages\Vars::get('page_' . $page . '_contains_' . $module);
    }

    $result = Database::query('SELECT content, module, `type` FROM ' .
            Database::tableName('content') . " WHERE slug = '" . Database::escapeValue($page) . "'");

    if (! Database::any($result)) {
        return $containsModule;
    }

    $dataset = Database::fetchAssoc($result);
    $content = $dataset['content'];
    $content = str_replace('&quot;', '"', $content);

    // TODO: Refactor this
    if ($dataset['module'] !== null && ! empty($dataset['module']) && $dataset['type'] == 'module') {
        if (! $module || ($module && $dataset['module'] == $module)) {
            $containsModule = true;
        }
    } elseif (stringContainsShortCodes($content, $module)) {
        $containsModule = true;
    }

    \App\Storages\Vars::set('page_' . $page . '_contains_' . $module, $containsModule);
    return $containsModule;
}
