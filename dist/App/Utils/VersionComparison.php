<?php

namespace App\Utils;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

/**
 * Utils for version number comparisons
 */
class VersionComparison
{
    /**
     * Compares two version numbers using the given operator
     * @param string|null $version1
     * @param string|null $version2
     * @param string $operator
     * @return bool
     */
    public static function compare(
        ?string $version1,
        ?string $version2,
        string $operator = '>'
    ): bool {
        $splitted1 = explode('.', $version1 ?? '');
        $splitted2 = explode('.', $version2 ?? '');
        $fillUp = self::fillUpVersionNumbers($splitted1, $splitted2);
        $splitted1 = $fillUp[0];
        $splitted2 = $fillUp[1];

        if ($operator === '=') {
            return $splitted1 === $splitted2;
        }

        if ($version1 === null && $version2 === null) {
            return true;
        }

        if ($version1 !== null && $version2 === null) {
            return false;
        }

        if ($operator === '>' && $splitted1 > $splitted2) {
            return true;
        }

        if ($operator === '>=' && $splitted1 >= $splitted2) {
            return true;
        }


        if ($operator === '<' && $splitted1 < $splitted2) {
            return true;
        }

        if ($operator === '<=' && $splitted1 <= $splitted2) {
            return true;
        }

        return false;
    }

    /**
     * Fill up two version numbers
     * @param array $splitted1
     * @param array $splitted2
     * @return array
     */
    public static function fillUpVersionNumbers(array $splitted1, array $splitted2): array
    {
        if (count($splitted1) === count($splitted2)) {
            return [$splitted1, $splitted2];
        }
        if (count($splitted1) > count($splitted2)) {
            $splitted2[] = '0';
            $difference = count($splitted1) - count($splitted2);
            for ($i = 0; $i < $difference; $i++) {
                $splitted2[] = '0';
            }
        } elseif (count($splitted1) < count($splitted2)) {
            $difference = count($splitted2) - count($splitted1);
            for ($i = 0; $i < $difference; $i++) {
                $splitted1[] = '0';
            }
        }
        return [$splitted1, $splitted2];
    }
}
