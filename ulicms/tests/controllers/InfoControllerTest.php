<?php

class InfoControllerTest extends \PHPUnit\Framework\TestCase {
    public function setUp(){
        clearCache();
    }
    public function tearDown(){
        clearCache();
    }
    
    public function testFetchChangelog(){
        $controller = new InfoController();
        $this->assertStringContainsString("Neues in UliCMS 2020", 
                $controller->fetchChangelog());
    }
    
      public function testGetChangelogInTextarea(){
        $controller = new InfoController();
        $this->assertStringContainsString(
                "Neues in UliCMS 2020", 
                $controller->getChangelogInTextarea()
                );
        $this->assertStringContainsString(
                '<textarea name="changelog" rows="15" cols="80" '
                . 'readonly="readonly">', 
                $controller->getChangelogInTextarea()
                );
    }
}
