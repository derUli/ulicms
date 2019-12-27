<?php

declare(strict_types=1);

// returns version number of UliCMS Core
function cms_version(): string {
    $v = new UliCMSVersion();
    return implode(".", $v->getInternalVersion());
}

function get_environment(): string {
    return getenv("ULICMS_ENVIRONMENT") ?
            getenv("ULICMS_ENVIRONMENT") : "default";
}

function func_enabled(string $func): array {
    $disabled = explode(',', ini_get('disable_functions'));
    foreach ($disabled as $disableFunction) {
        $is_disabled[] = trim($disableFunction);
    }
    if (faster_in_array($func, $is_disabled)) {
        $it_is_disabled["m"] = $func .
                '() has been disabled for security reasons in php.ini';
        $it_is_disabled["s"] = 0;
    } else {
        $it_is_disabled["m"] = $func . '() is allow to use';
        $it_is_disabled["s"] = 1;
    }
    return $it_is_disabled;
}

