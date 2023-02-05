<?php

namespace UliCMS\Utils\VersionComparison;

/**
 * Compare two version numbers
 * @param string|null $version1 Version Number
 * @param string|null $version2 Version Number
 * @param string $operator Comparions Operator
 * @return bool Result of Comparison
 */
function compare(
        ?string $version1,
        ?string $version2,
        string $operator = ">"
): bool {

    if ($version1 === null && $version2 === null) {
        return true;
    }

    if ($version1 !== null && $version2 === null) {
        return false;
    }

    $splitted1 = explode(".", $version1 ?? '');
    $splitted2 = explode(".", $version2 ?? '');
    $fillUp = fillUpVersionNumbers($splitted1, $splitted2);
    $splitted1 = $fillUp[0];
    $splitted2 = $fillUp[1];

    if ($operator === "=") {
        return $splitted1 === $splitted2;
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
 * Compare if Version Number 1 is greater than 2
 * @param string|null $version1 Version Number 1
 * @param string|null $version2 Version Number 2
 * @return bool Result of Comparison
 */
function isGreater(
        ?string $version1,
        ?string $version2
): bool {
    return compare($version1, $version2, ">");
}

/**
 * Compare if Version Number 1 is greater or equal than 2
 * @param string|null $version1 Version Number 1
 * @param string|null $version2 Version Number 2
 * @return bool Result of Comparison
 */
function isGreaterOrEqual(
        ?string $version1,
        ?string $version2
): bool {
    return compare($version1, $version2, ">=");
}

/**
 * Compare if Version Number 1 is lesser than 2
 * @param string|null $version1 Version Number 1
 * @param string|null $version2 Version Number 2
 * @return bool Result of Comparison
 */
function isLesser(
        ?string $version1,
        ?string $version2
): bool {
    return compare($version1, $version2, "<");
}

/**
 * Compare if Version Number 1 is lesser or equal than 2
 * @param string|null $version1 Version Number 1
 * @param string|null $version2 Version Number 2
 * @return bool Result of Comparison
 */
function isLesserOrEqual(
        ?string $version1,
        ?string $version2
): bool {
    return compare($version1, $version2, "<=");
}

/**
 * Compare if Version Number 1 is equal to 2
 * @param string|null $version1 Version Number 1
 * @param string|null $version2 Version Number 2
 * @return bool Result of Comparison
 */
function isEqual(
        ?string $version1,
        ?string $version2
): bool {
    return compare($version1, $version2, "=");
}

/**
 * Version Fill up version numbers with 0
 * @param array $splitted1 Version array
 * @param array $splitted2 Version array
 * @return array of arrays in format [[1, 3, 0], [1, 4, 0]]
 */
function fillUpVersionNumbers(array $splitted1, array $splitted2): array {
    if (count($splitted1) === count($splitted2)) {
        return [$splitted1, $splitted2];
    }
    if (count($splitted1) > count($splitted2)) {
        $splitted2[] = "0";
        $difference = count($splitted1) - count($splitted2);
        for ($i = 0; $i < $difference; $i++) {
            $splitted2[] = "0";
        }
    } elseif (count($splitted1) < count($splitted2)) {
        $difference = count($splitted2) - count($splitted1);
        for ($i = 0; $i < $difference; $i++) {
            $splitted1[] = "0";
        }
    }
    return [$splitted1, $splitted2];
}
