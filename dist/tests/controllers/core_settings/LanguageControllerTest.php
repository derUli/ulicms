<?php

use App\Models\Content\Language;

class LanguageControllerTest extends \PHPUnit\Framework\TestCase {
    private $lang = null;

    private $initialSettings = [];

    protected function setUp(): void {
        $this->initialDefaultLanguage = [
            'system_language' => Settings::get('system_language'),
            'default_language' => Settings::get('default_language')
        ];
    }

    protected function tearDown(): void {
        $_GET = [];
        $_POST = [];
        $sql = "delete from `{prefix}languages` where language_code <> 'de' and language_code <> 'en'";

        Database::query($sql, true);

        foreach ($this->initialSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testCreatePostReturnsModel(): void {
        $_POST['name'] = 'Lampukisch';
        $_POST['language_code'] = 'lp';
        $controller = new LanguageController();
        $model = $controller->_createPost();
        $this->assertInstanceOf(Language::class, $model);

        $language = new Language($model->getID());
        $this->assertEquals('Lampukisch', $language->getName());
        $this->assertEquals('lp', $language->getLanguageCode());
    }

    public function testDeleteReturnTrue(): void {
        $lang = new Language();
        $lang->setName('Lampukisch');
        $lang->setLanguageCode('lp');
        $lang->save();

        $_GET['id'] = $lang->getId();
        $controller = new LanguageController();
        $this->assertTrue($controller->_deletePost());
    }

    public function testDeleteReturnsFalse(): void {
        $_GET['id'] = PHP_INT_MAX;
        $controller = new LanguageController();
        $this->assertFalse($controller->_deletePost());
    }

    public function testDefaultLanguage(): void {
        $this->assertNotEquals('it', Settings::get('system_language'));
        $this->assertNotEquals('it', Settings::get('default_language'));

        $_POST['default'] = 'it';
        $controller = new LanguageController();
        $controller->_setDefaultLanguage();

        $this->assertEquals('it', Settings::get('system_language'));
        $this->assertEquals('it', Settings::get('default_language'));
    }
}
