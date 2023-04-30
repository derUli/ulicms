<?php

declare(strict_types=1);

namespace App\UliCMS\SystemRequirementsChecker\Checks;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Load composer.json
 */
trait ComposerCheck {
    public function getComposerJson() {
        $jsonFile = ULICMS_ROOT . '/composer.json';
        $readFile = file_get_contents($jsonFile);
        return json_decode($readFile, true);
    }
}
