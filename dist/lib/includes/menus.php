<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

/**
 * Get all menus that have content
 *
 * @return array<string>
 */
function get_all_used_menus(): array {
    $retval = [];
    $result = Database::query('select menu from {prefix}content group by menu', true);
    while ($row = Database::fetchObject($result)) {
        $retval[] = $row->menu;
    }
    return $retval;
}

/**
 * Get all menus
 *
 * @param bool $only_used
 * @param bool $read_theme_menus
 *
 * @return array<string>
 */
function get_all_menus(
    bool $only_used = false,
    bool $read_theme_menus = true
): array {
    $menus = [
        'left',
        'top',
        'right',
        'bottom',
        'not_in_menu'
    ];

    if ($only_used) {
        $used = get_all_used_menus();
        $new_menus = [];

        $menuCount = count($menus);

        for ($i = 0; $i < $menuCount; $i++) {
            if (in_array($menus[$i], $used)) {
                $new_menus[] = $menus[$i];
            }
        }
        $menus = $new_menus;
    }

    $themesList = getAllThemes();
    $allThemeMenus = [];
    foreach ($themesList as $theme) {
        $themeMenus = getThemeMeta($theme, 'menus');
        if ($themeMenus && is_array($themeMenus)) {
            foreach ($themeMenus as $m) {
                if (! in_array($m, $allThemeMenus)) {
                    $allThemeMenus[] = $m;
                }
            }
        }
    }

    if ($read_theme_menus && count($allThemeMenus) > 0) {
        $menus = $allThemeMenus;
    }

    if (! in_array('not_in_menu', $menus)) {
        $menus[] = 'not_in_menu';
    }

    sort($menus);
    return $menus;
}
