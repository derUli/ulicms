<?php

declare(strict_types=1);

namespace App\UliCMS\SystemRequirementsChecker\Checks;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Check minimum PHP Version
 */
class PHPVersionCheck implements CheckInterface {
    use ComposerCheck;

    public function name(): string {
        $composerJson = $this->getComposerJson();
        $version = $composerJson['require']['php'];
        return "php{$version}";
    }

    public function expected(): string {
        $composerJson = $this->getComposerJson();
        return $composerJson['require']['php'];
    }

    public function actual(): string {
        return phpversion();
    }

    public function isFulfilled(): bool {
        return false;
    }
}
