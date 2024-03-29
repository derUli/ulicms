<?php

declare(strict_types=1);

namespace App\Security\SpamChecker;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

interface SpamCheckerInterface {
    // this must be an array which must return an array of
    // SpamDetectionResults
    public function getErrors(): array;

    // this must be a function which return the errors array
    public function clearErrors(): void;

    // this must perform all configured spam checks
    // and fill the errors array with SpamDetectionResults
    public function doSpamCheck(): bool;
}
