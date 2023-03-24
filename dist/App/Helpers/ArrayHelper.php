<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use App\Helpers\Helper;
use Exception;

/**
 * This class contains utilities to deal with arrays
 */
class ArrayHelper extends Helper
{
    /**
     * inserts an item before an index to an array
     * @param array $input
     * @param int $index
     * @param type $element
     * @return array
     * @throws Exception
     */
    public static function insertBefore(
        array $input,
        int $index,
        $element
    ): array {
        if (!array_key_exists($index, $input)) {
            throw new Exception("Index not found");
        }
        $tmpArray = [];
        $originalIndex = 0;
        foreach ($input as $key => $value) {
            if ($key === $index) {
                $tmpArray[] = $element;
                break;
            }
            $tmpArray[$key] = $value;
            $originalIndex++;
        }
        array_splice($input, 0, $originalIndex, $tmpArray);
        return $input;
    }

    /**
     * inserts an item after an index to an array
     * @param array $input
     * @param int $index
     * @param type $element
     * @return array
     * @throws Exception
     */
    public static function insertAfter(
        array $input,
        int $index,
        $element
    ): array {
        if (!array_key_exists($index, $input)) {
            throw new Exception("Index not found");
        }
        $tmpArray = [];
        $originalIndex = 0;
        foreach ($input as $key => $value) {
            $tmpArray[$key] = $value;
            $originalIndex++;
            if ($key === $index) {
                $tmpArray[] = $element;
                break;
            }
        }
        array_splice($input, 0, $originalIndex, $tmpArray);
        return $input;
    }

    /**
     * Checks if an array has a list of keys
     * @param array|null $input
     * @param array $keys
     * @return bool
     */
    public static function hasMultipleKeys(?array $input, array $keys): bool
    {
        if (!$input) {
            return false;
        }

        $hasKeys = true;

        foreach ($keys as $key) {
            if (!array_key_exists($key, $input)) {
                $hasKeys = false;
            }
        }
        return $hasKeys;
    }
}
