<?php

declare(strict_types=1);

/**
 * Returns font size values for design settings in backend from 6px to 80px
 * @global type $sizes
 * @return array
 */
function getFontSizes(): array {
    $sizes = [];
    for ($i = 6; $i <= 80; $i++) {
        $sizes[] = $i . "px";
    }
    do_event("custom_font_sizes");

    $sizes = apply_filter($sizes, "font_sizes");
    return $sizes;
}

/**
 * Get metadata of theme
 * @param Name of theme
 * @param string $attrib Attribute
 * @return type if $attrib is not null the value of a specific attribute, else an array
 */
function getThemeMeta(string $theme, string $attrib = null) {
    $retval = null;
    $metadata_file = getTemplateDirPath($theme, true) . "metadata.json";
    if (file_exists($metadata_file)) {
        $data = !Vars::get("theme_{$theme}_meta") ?
                file_get_contents($metadata_file) : Vars::get("theme_{$theme}_meta");

        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        Vars::set("theme_{$theme}_meta", $data);
        if ($attrib != null) {
            if (isset($data[$attrib])) {
                $retval = $data[$attrib];
            }
        } else {
            $retval = $data;
        }
    }
    return $retval;
}
