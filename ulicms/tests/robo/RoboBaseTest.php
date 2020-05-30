<?php

declare(strict_types=1);

require_once "RoboFile.php";
require_once __DIR__ . "/RoboTestFile.php";

abstract class RoboBaseTest extends \PHPUnit\Framework\TestCase {

    protected function runRoboCommand(array $command): string {
        $runner = new Robo\Runner(RoboTestFile::class);
        array_unshift($command, "vendor/bin/robo");
        ob_start();
        $runner->execute($command);
        return trim(ob_get_clean());
    }

}
