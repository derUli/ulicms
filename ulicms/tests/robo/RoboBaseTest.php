<?php

require_once "RoboFile.php";
require_once __DIR__."/RoboTestFile.php";

abstract class RoboBaseTest extends \PHPUnit\Framework\TestCase{

    protected function runRoboCommand($command) {
       $runner = new Robo\Runner(RoboTestFile::class);
       array_unshift($command, "vendor/bin/robo");
       ob_start();
       $runner->execute($command);
       return ob_get_clean();
    }

}
