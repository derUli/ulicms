<?php

declare(strict_types=1);

namespace App\Models\Content\Types;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class ContentType
{
    public $show = [];

    public $customFieldTabTitle;

    public $customFields = [];
}
