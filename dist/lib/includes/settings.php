<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');

/**
 * Get a setting
 * @param string $key
 * @deprecated since 2023.1
 * @return type
 */
function getconfig(string $key)
{
    return Settings::get($key);
}

/**
 * @deprecated since 2023.1
 * Deletes a setting
 * @param string $key
 * @return bool
 */
// Remove an configuration variable
function deleteconfig(string $key): bool
{
    return Settings::delete($key);
}

/**
 *
 * Set a setting
 * @param string $key
 * @param type $value
 * @deprecated since 2023.1
 */
function setconfig(string $key, $value)
{
    Settins::set($key, $value);
}

/**
 * Get a language specific setting
 * @param string $name
 * @param string $lang
 * @return string|null
 */
function get_lang_config(string $name, string $lang): ?string
{
    return Settings::getLang($name, $lang);
}
