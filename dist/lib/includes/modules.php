<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

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
        $main_class = getModuleMeta($module, 'main_class');
        $controller = $main_class ? ControllerRegistry::get($main_class) : null;

        $escapedName = \App\Helpers\ModuleHelper::underscoreToCamel($type . '_filter');
        if ($controller && method_exists($controller, $escapedName)) {
            $text = $controller->{$escapedName}($text);
        }
    }

    return $text;
}

function do_event(
    string $name
): void {
    $manager = new \App\Packages\ModuleManager();
    $modules = $manager->getEnabledModuleNames();

    foreach($modules as $module) {
        $main_class = getModuleMeta($module, 'main_class');
        $controller = null;

        if ($main_class) {
            $controller = ControllerRegistry::get($main_class);
        }

        ob_start();
        $escapedName = \App\Helpers\ModuleHelper::underscoreToCamel($name);
        if ($controller && method_exists($controller, $escapedName)) {
            echo $controller->{$escapedName}();
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
        $embedCodeVariants = [
            '[module="' . $module . '"]',
            '[module=&quot;' . $module . '&quot;]',
            '[module=' . $module . ']'
        ];

        $main_class = getModuleMeta($module, 'main_class');
        $controller = $main_class ? ControllerRegistry::get($main_class) : null;

        $html_output = '';

        if ($controller && method_exists($controller, 'render')) {
            $html_output = $controller->render();
        } elseif (function_exists($module . '_render')) {
            $html_output = call_user_func($module . '_render');
        }

        foreach($embedCodeVariants as $embedCodeVariant) {
            $string = str_replace($embedCodeVariants, $html_output, $string);
        }

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
