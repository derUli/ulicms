<?php

require_once 'RoboFile.php';

class RoboTestFile extends RoboFile {
    public function write($text): void {
        echo $text;
    }

    public function writeln($text): void {
        $this->write($text . PHP_EOL);
    }
}
