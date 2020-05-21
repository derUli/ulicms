<?php

require_once "RoboFile.php";

class RoboTestFile extends RoboFile {

    public function writeln($text) {
        echo $text . "\n";
    }

}
