<?php

declare(strict_types=1);

// get page id by slug
function getPageIDBySlug(string $slug): ?int {
    $result = db_query("SELECT slug, id FROM `" . tbname("content")
            . "` where slug='" . db_escape($slug) . "'");
    if (db_num_rows($result) > 0) {
        $row = db_fetch_object($result);
        return intval($row->id);
    }
    return null;
}

function getPageSlugByID(?int $id): ?string {
    $result = db_query("SELECT slug, id FROM `" . tbname("content")
            . "` where id=" . intval($id));
    if (db_num_rows($result) > 0) {
        $row = db_fetch_object($result);
        return $row->slug;
    }
    return null;
}

function getPageByID(int $id): ?object {
    $id = intval($id);
    $result = db_query("SELECT * FROM " . tbname("content") .
            " where id = " . $id);
    if (db_num_rows($result) > 0) {
        return db_fetch_object($result);
    }
    return null;
}

function getPageTitleByID(?int $id): string {
    $result = db_query("SELECT title, id FROM `" . tbname("content")
            . "` where id=" . intval($id));
    if (db_num_rows($result) > 0) {
        $row = db_fetch_object($result);
        return $row->title;
    }
    return "[" . get_translation("none") . "]";
}

// Get slugs of all pages
function getAllPagesWithTitle(): array {
    $result = db_query("SELECT slug, id, title FROM `" . tbname("content") .
            "` WHERE `deleted_at` IS NULL ORDER BY slug");
    $returnvalues = [];
    while ($row = db_fetch_object($result)) {
        $a = Array(
            $row->title,
            $row->slug . ".html"
        );
        array_push($returnvalues, $a);
        if (containsModule($row->slug, "blog")) {

            $sql = "select title, seo_shortname from " . tbname("blog")
                    . " ORDER by datum DESC";
            $query_blog = db_query($sql);
            while ($row_blog = db_fetch_object($query_blog)) {
                $title = $row->title . " -> " . $row_blog->title;
                $url = $row->slug . ".html" . "?single=" .
                        $row_blog->seo_shortname;
                $b = [
                    $title,
                    $url
                ];
                array_push($returnvalues, $b);
            }
        }
    }
    return $returnvalues;
}

// Get all pages
function getAllPages(
        string $lang = null,
        string $order = "slug",
        bool $exclude_hash_links = true,
        string $menu = null
): array {
    if (!$lang) {
        if (!$menu) {
            $result = db_query("SELECT * FROM `" . tbname("content") .
                    "` WHERE `deleted_at` IS NULL ORDER BY $order");
        } else {
            $result = db_query("SELECT * FROM `" . tbname("content") .
                    "` WHERE `deleted_at` IS NULL and menu = '" .
                    Database::escapeValue($menu) . "' ORDER BY $order");
        }
    } else {
        if (!$menu) {
            $result = db_query("SELECT * FROM `" . tbname("content") .
                    "` WHERE `deleted_at` IS NULL AND language ='" .
                    db_escape($lang) . "' ORDER BY $order");
        } else {
            $result = db_query("SELECT * FROM `" . tbname("content") .
                    "` WHERE `deleted_at` IS NULL AND language ='" .
                    db_escape($lang) . "' and menu = '" .
                    Database::escapeValue($menu) . "' ORDER BY $order");
        }
    }
    $returnvalues = [];
    while ($row = db_fetch_assoc($result)) {
        if (!$exclude_hash_links or ( $exclude_hash_links
                and $row["type"] != "link" and $row["type"] != "node"
                and $row["type"] != "language_link")) {
            array_push($returnvalues, $row);
        }
    }

    return $returnvalues;
}

// Get slugs of all pages
function getAllSlugs(string $lang = null): array {
    $slugs = [];

    if (!$lang) {
        $result = db_query("SELECT slug,id FROM `" . tbname("content") .
                "` WHERE `deleted_at` IS NULL AND link_url "
                . "NOT LIKE '#%' ORDER BY slug");
    } else {

        $result = db_query("SELECT slug,id FROM `" . tbname("content") .
                "` WHERE `deleted_at` IS NULL  AND link_url "
                . "NOT LIKE '#%' AND language ='" . db_escape($lang) .
                "' ORDER BY slug");
    }
    while ($row = db_fetch_object($result)) {
        array_push($slugs, $row->slug);
    }

    return $slugs;
}