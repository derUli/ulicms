<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * This class contains helper methods to deal with boolean vars
 */
abstract class BooleanHelper {
    /**
     * Converts a boolean to a humand readable string
     *
     * @param bool $value
     * @param ?string $yesString
     * @param ?string $noString
     *
     * @return string
     */
    public static function bool2YesNo(
        bool $value,
        ?string $yesString = null,
        ?string $noString = null
    ): string {
        if (! $yesString) {
            $yesString = get_translation('yes');
        }
        if (! $noString) {
            $noString = get_translation('no');
        }
        return $value ? $yesString : $noString;
    }
}
