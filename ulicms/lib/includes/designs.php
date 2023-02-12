<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');

/**
 * Gets metadata of a theme
 * @param string $theme
 * @param string $attrib
 * @return type
 */
function getThemeMeta(string $theme, string $attrib = null)
{
    $retval = null;
    $metadata_file = getTemplateDirPath($theme, true) . 'metadata.json';

    if (is_file($metadata_file)) {
        
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
