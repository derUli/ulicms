<?php

declare(strict_types=1);

namespace App\Storages\Settings;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Use this interface to implement On/Off toggle switches
 */
interface ToggleInterface {
    public function isEnabled(): bool;

    public function enable(): void;

    public function disable(): void;

    public function toggle(): bool;
}
