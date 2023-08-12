<?php

class CustomDataTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        $_GET['slug'] = 'welcome';
        $_SESSION['language'] = 'en';
    }

    protected function tearDown(): void {
        App\Models\Content\CustomData::delete('my_value');
        unset($_GET['slug'], $_SESSION['language']);

    }

    public function testGetCustomDataOrSetting(): void {
        Settings::set('my_value', 'text1');
        $this->assertEquals('text1', App\Models\Content\CustomData::getCustomDataOrSetting('my_value'));
        App\Models\Content\CustomData::set('my_value', 'text2');
        $this->assertEquals('text2', App\Models\Content\CustomData::getCustomDataOrSetting('my_value'));
        App\Models\Content\CustomData::delete('my_value');
        $this->assertEquals('text1', App\Models\Content\CustomData::getCustomDataOrSetting('my_value'));
    }

    public function testGetReturnsEmpty(): void {
        $this->assertEquals(
            [],
            App\Models\Content\CustomData::get()
        );
    }

    public function testGetDefaultJSON(): void {
        $json = App\Models\Content\CustomData::getDefaultJSON();
        $this->assertNotEmpty($json);
        $this->assertTrue(is_json($json));
    }

    public function testGetDefaultReturnsEmptyArray(): void {
        $this->assertNull(App\Models\Content\CustomData::getDefault('unknown_type'));
    }

    public function testGetDefaultReturnsArray(): void {
        App\Models\Content\CustomData::setDefault('some_data', '123');
        $this->assertEquals(
            '123',
            App\Models\Content\CustomData::getDefault('some_data')
        );
    }
}
