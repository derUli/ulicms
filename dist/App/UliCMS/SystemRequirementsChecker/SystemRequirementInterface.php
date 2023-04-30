<?php

declare(strict_types=1);

namespace App\UliCMS\SystemRequirementsChecker;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Use this interface to implement On/Off toggle switches
 */
interface SystemRequirementInterface {
    public function ruleName(): string;

    public function ruleDisplayName(): string;

    public function expected(): string;

    public function actual(): string;

    public function isFulfilled(): bool;
}
