<?php

declare(strict_types=1);

namespace UliCMS\Helpers;

use Helper;
use Exception;

class ArrayHelper extends Helper
{

    // inserts an item before an index to an array
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

    // inserts an item after an index to an array
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

    // returns the first x items of an array
    public static function take(int $count, $data)
    {
        if (is_array($data)) {
            return array_slice($data, 0, $count);
        } elseif (is_string($data)) {
            return mb_substr($data, 0, $count);
        }
        return null;
    }

    // returns true if an array contains only one item
    public static function isSingle(array $input): bool
    {
        return count($input) == 1;
    }

    // returns the array item if the array contains only one item
    // else returns null
    public static function getSingle(array $input)
    {
        if (self::isSingle($input)) {
            return $input[0];
        }
        return null;
    }

    // flatten a nested array structure to one layer
    public static function flatten($input): array
    {
        if (!is_array($input)) {
            // nothing to do if it's not an array
            return [$input];
        }

        $result = [];
        foreach ($input as $value) {
            // explode the sub-array, and add the parts
            $result = array_merge($result, self::flatten($value));
        }

        return $result;
    }

    public static function getValueOrDefault(?array $input, $key, $default)
    {
        if (!$input) {
            return $default;
        }

        return isset($input[$key]) ? $input[$key] : $default;
    }
    
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
