<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class HomeViewModel
{
    public $contentCount = 0;

    public $topPages = [];

    public $lastModfiedPages = [];

    public $admins = [];

    public $guestbookEntryCount = null;
}
