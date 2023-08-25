<?php

declare(strict_types=1);

namespace App\Models\Packages;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

interface PackageInterface {
    /**
     * Uninstall a package
     */
    public function uninstall(): bool;
}
