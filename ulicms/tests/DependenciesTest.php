<?php

class DependenciesTest extends \PHPUnit\Framework\TestCase {

    public function testPhpIcoInstalled() {
        $this->assertTrue(class_exists("PHP_ICO"));
    }

}
