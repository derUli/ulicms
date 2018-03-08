<?php
use \YaLinqo\Enumerable;

class YaLinqoTest extends PHPUnit_Framework_TestCase {
     // Test if YaLinqo Framework is correctly installed
     // Yet Another LINQ to Objects for PHP
     public function testYaLinqoInstalled(){
       $this->assertTrue(class_exists("\\YaLinqo\\Enumerable"));
     }
}
