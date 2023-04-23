<?php

class CustomDataFunctionsTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        $_GET['slug'] = 'welcome';
        $_SESSION['language'] = 'en';
    }

    protected function tearDown(): void {
        Settings::delete('my_value');
        delete_custom_data('my_value');
        unset($_GET['slug'], $_SESSION['language']);

    }

    public function testSetAndGetCustomData() {
        $this->assertEquals([], get_custom_data('hello'));
        set_custom_data('hello', 'world');

        $this->assertEquals(
            ['hello' => 'world'],
            get_custom_data()
        );

        delete_custom_data();
        $this->assertEquals([], get_custom_data());
    }

    public function testGetReturnsNull() {
        $this->assertEquals([], get_custom_data('gibts_echt_nicht'));
    }
}
