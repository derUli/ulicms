<?php

declare(strict_types=1);

namespace App\UliCMS\SystemRequirementsChecker\Checks;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Use this interface to implement system requirement checks
 */
interface CheckInterface {
    public function name(): string;

    public function expected(): string;

    public function actual(): string;

    public function isFulfilled(): bool;
}
