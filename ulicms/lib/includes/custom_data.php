<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');

function get_custom_data(?string $page = null): array
{
    return CustomData::get($page);
}

function set_custom_data(string $var, $value, ?string $page = null): void
{
    CustomData::set($var, $value, $page);
}

function delete_custom_data(?string $var = null, ?string $page = null): void
{
    CustomData::delete($var, $page);
    return;
}
