<?php

declare(strict_types=1);

function get_all_used_menus(): array {
    $retval = [];
    $result = db_query("select menu from " . tbname("content") .
            " group by menu");
    while ($row = db_fetch_object($result)) {
        $retval[] = $row->menu;
    }
    return $retval;
}

// Gibt die Identifier aller Menüs zurück.
// Zusätzliche Navigationsmenüs können definiert werden,
// durch setzen von additional_menus
function getAllMenus(
        bool $only_used = false,
        bool $read_theme_menus = true
): array {
    $menus = Array(
        "left",
        "top",
        "right",
        "bottom",
        "not_in_menu"
    );
    $additional_menus = Settings::get("additional_menus");

    if ($additional_menus) {
        $additional_menus = explode(";", $additional_menus);
        foreach ($additional_menus as $m) {
            array_push($menus, $m);
        }
    }
    if ($only_used) {
        $used = get_all_used_menus();
        $new_menus = [];
        for ($i = 0; $i <= count($menus); $i ++) {
            if (faster_in_array($menus[$i], $used)) {
                $new_menus[] = $menus[$i];
            }
        }
        $menus = $new_menus;
    }

    $themesList = getAllThemes();
    $allThemeMenus = [];
    foreach ($themesList as $theme) {
        $themeMenus = getThemeMeta($theme, "menus");
        if ($themeMenus and is_array($themeMenus)) {
            foreach ($themeMenus as $m) {
                if (!faster_in_array($m, $allThemeMenus)) {
                    $allThemeMenus[] = $m;
                }
            }
        }
    }

    if ($read_theme_menus and count($allThemeMenus) > 0) {
        $menus = $allThemeMenus;
    }

    if (!faster_in_array("not_in_menu", $menus)) {
        $menus[] = "not_in_menu";
    }

    sort($menus);
    return $menus;
}
