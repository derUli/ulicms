<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Models\Content\Types\DefaultContentTypes;

function get_used_post_types(): array
{
    $result = Database::query('select `type` from {prefix}content '
                    . 'group by `type`', true);
    $types = get_available_post_types();
    $used_types = [];
    $return_types = [];
    while ($row = Database::fetchObject($result)) {
        $used_types[] = $row->type;
    }
    foreach ($types as $type) {
        if (in_array($type, $used_types)) {
            $return_types[] = $type;
        }
    }
    return $return_types;
}

function get_available_post_types(): array
{
    $types = DefaultContentTypes::getAll();
    $types = array_keys($types);
    return $types;
}
