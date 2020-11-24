<?php

use UliCMS\Exceptions\NotImplementedException;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PathTest
 *
 * @author ulric
 */
class PathTest extends \PHPUnit\Framework\TestCase {

    public function testNormalize() {
        $this->assertStringContainsString(
                DIRECTORY_SEPARATOR,
                Path::normalize('..\foo\bar\file.txt')
        );

        $this->assertStringContainsString(
                DIRECTORY_SEPARATOR,
                Path::normalize('../foo/bar/file.txt')
        );
    }
    
    public function testResolve(){
        throw new NotImplementedException();
    }
    public function testResolveAndNormalize(){
        throw new NotImplementedException();
    }

}
