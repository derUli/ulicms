<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

function getPageSlugByID(?int $id): ?string {
    $result = Database::query('SELECT slug, id FROM `' . Database::tableName('content')
            . '` where id=' . (int)$id);
    if (Database::getNumRows($result) > 0) {
        $row = Database::fetchObject($result);
        return $row->slug;
    }
    return null;
}

function getPageByID(int $id): ?object {
    $id = (int)$id;
    $result = Database::query('SELECT * FROM ' . Database::tableName('content') .
            ' where id = ' . $id);
    if (Database::getNumRows($result) > 0) {
        return  Database::fetchObject($result);
    }
    return null;
}

function getPageTitleByID(?int $id): string {
    $result = Database::query('SELECT title, id FROM `' . Database::tableName('content')
            . '` where id=' . (int)$id);
    if (Database::getNumRows($result) > 0) {
        $row = Database::fetchObject($result);
        return $row->title;
    }
    return '[' . get_translation('none') . ']';
}

// Get slugs of all pages
function getAllPagesWithTitle(): array {
    $result = Database::query('SELECT slug, id, title FROM `' . Database::tableName('content') .
            '` WHERE `deleted_at` IS NULL ORDER BY slug');
    $returnvalues = [];
    while ($row = Database::fetchObject($result)) {
        $a = [
            $row->title,
            $row->slug
        ];
        $returnvalues[] = $a;
    }
    return $returnvalues;
}

// Get all pages
function getAllPages(
    ?string $lang = null,
    string $order = 'slug',
    bool $exclude_hash_links = true,
    ?string $menu = null
): array {
    if (! $lang) {
        if (! $menu) {
            $result = Database::query('SELECT * FROM `' . Database::tableName('content') .
                    "` WHERE `deleted_at` IS NULL ORDER BY {$order}");
        } else {
            $result = Database::query('SELECT * FROM `' . Database::tableName('content') .
                    "` WHERE `deleted_at` IS NULL and menu = '" .
                    Database::escapeValue($menu) . "' ORDER BY {$order}");
        }
    } else {
        if (! $menu) {
            $result = Database::query('SELECT * FROM `' . Database::tableName('content') .
                    "` WHERE `deleted_at` IS NULL AND language ='" .
                    Database::escapeValue($lang) . "' ORDER BY {$order}");
        } else {
            $result = Database::query('SELECT * FROM `' . Database::tableName('content') .
                    "` WHERE `deleted_at` IS NULL AND language ='" .
                    Database::escapeValue($lang) . "' and menu = '" .
                    Database::escapeValue($menu) . "' ORDER BY {$order}");
        }
    }
    $returnvalues = [];
    while ($row = Database::fetchAssoc($result)) {
        if (! $exclude_hash_links || ($exclude_hash_links
                && $row['type'] !== 'link' && $row['type'] !== 'node'
                && $row['type'] !== 'language_link')) {
            $returnvalues[] = $row;
        }
    }

    return $returnvalues;
}

// Get slugs of all pages
function getAllSlugs(?string $lang = null): array {
    $slugs = [];

    if (! $lang) {
        $result = Database::query('SELECT slug,id FROM `' . Database::tableName('content') .
                '` WHERE `deleted_at` IS NULL AND link_url '
                . "NOT LIKE '#%' ORDER BY slug");
    } else {
        $result = Database::query('SELECT slug,id FROM `' . Database::tableName('content') .
                '` WHERE `deleted_at` IS NULL  AND link_url '
                . "NOT LIKE '#%' AND language ='" . Database::escapeValue($lang) .
                "' ORDER BY slug");
    }
    while ($row = Database::fetchObject($result)) {
        $slugs[] = $row->slug;
    }

    return $slugs;
}
