<?php


class PackageTest extends \PHPUnit\Framework\TestCase
{
    public function testUninstallModuleWithDot(){
        $this->assertFalse(uninstall_module('..'));
    }
    
}
