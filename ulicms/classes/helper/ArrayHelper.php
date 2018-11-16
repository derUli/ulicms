<?php

class ArrayHelper
{

    public static function insertBefore($input, $index, $element)
    {
        if (! array_key_exists($index, $input)) {
            throw new Exception("Index not found");
        }
        $tmpArray = array();
        $originalIndex = 0;
        foreach ($input as $key => $value) {
            if ($key === $index) {
                $tmpArray[] = $element;
                break;
            }
            $tmpArray[$key] = $value;
            $originalIndex ++;
        }
        array_splice($input, 0, $originalIndex, $tmpArray);
        return $input;
    }

    public static function insertAfter($input, $index, $element)
    {
        if (! array_key_exists($index, $input)) {
            throw new Exception("Index not found");
        }
        $tmpArray = array();
        $originalIndex = 0;
        foreach ($input as $key => $value) {
            $tmpArray[$key] = $value;
            $originalIndex ++;
            if ($key === $index) {
                $tmpArray[] = $element;
                break;
            }
        }
        array_splice($input, 0, $originalIndex, $tmpArray);
        return $input;
    }

    public static function take($count, $data)
    {
        if (is_array($data)) {
            return array_slice($data, 0, $count);
        } else if (is_string($data)) {
            return mb_substr($data, 0, $count);
        }
        return null;
    }

    public static function isSingle($input)
    {
        return count($input) == 1;
    }

    public static function getSingle($input)
    {
        if (self::isSingle($input)) {
            return $input[0];
        }
        return null;
    }
}