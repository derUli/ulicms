<?php

use UliCMS\Models\Content\Language;

class LanguageControllerTest extends \PHPUnit\Framework\TestCase {

    private $lang = null;

    protected function setUp(): void {
        $lang = new Language();
        $lang->setName("Lampukisch");
        $lang->setLanguageCode("lp");
        $lang->save();
        $this->lang = $lang;
    }

    protected function tearDown(): void {
        $_GET = [];
        $sql = "delete from `{prefix}languages` where language_code <> 'de' and language_code <> 'en'";
        
        Database::query($sql, true);
    }

       public function testDeleteReturnTrue(): void {
        $_GET["id"] = $this->lang->getId();
        $controller = new LanguageController();
        $this->assertTrue($controller->_deletePost());
    }

    public function testDeleteReturnsFalse(): void {
        $_GET["id"] = PHP_INT_MAX;
        $controller = new LanguageController();
        $this->assertFalse($controller->_deletePost());
    }

}
