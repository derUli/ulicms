<?php

declare(strict_types=1);

namespace App\Constants;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

abstract class ModuleEvent {
    public const RUNS_ONCE = 'once';

    public const RUNS_MULTIPLE = 'multiple';
}
