<?php

use UliCMS\Models\Content\Language;

class LanguageControllerTest extends \PHPUnit\Framework\TestCase {

    private $lang = null;

    protected function tearDown(): void {
        $_GET = [];
        $sql = "delete from `{prefix}languages` where language_code <> 'de' and language_code <> 'en'";

        Database::query($sql, true);
    }

    public function testCreatePostReturnsModel(): void {
        $_POST["name"] = "Lampukisch";
        $_POST["language_code"] = "lp";
        $controller = new LanguageController();
        $model = $controller->_createPost();
        $this->assertInstanceOf(Language::class, $model);


        $language = new Language($model->getID());
        $this->assertEquals("Lampukisch", $language->getName());
        $this->assertEquals("lp", $language->getLanguageCode());
    }

    public function testDeleteReturnTrue(): void {
        $lang = new Language();
        $lang->setName("Lampukisch");
        $lang->setLanguageCode("lp");
        $lang->save();

        $_GET["id"] = $lang->getId();
        $controller = new LanguageController();
        $this->assertTrue($controller->_deletePost());
    }

    public function testDeleteReturnsFalse(): void {
        $_GET["id"] = PHP_INT_MAX;
        $controller = new LanguageController();
        $this->assertFalse($controller->_deletePost());
    }

}
