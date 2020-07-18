<?php

declare(strict_types=1);

namespace UliCMS\Security\SpamChecker;

interface ISpamChecker
{

    // this must be an array which must return an array of
    // SpamDetectionResults
    public function getErrors(): array;

    // this must be a function which return the errors array
    public function clearErrors(): void;

    // this must perform all configured spam checks
    // and fill the errors array with SpamDetectionResults
    public function doSpamCheck(): bool;
}
