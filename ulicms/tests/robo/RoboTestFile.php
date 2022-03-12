<?php

require_once "RoboFile.php";

class RoboTestFile extends RoboFile {

    public function write($text) {
        echo $text;
    }

    public function writeln($text) {
        $this->write("$text\n");
    }

}
