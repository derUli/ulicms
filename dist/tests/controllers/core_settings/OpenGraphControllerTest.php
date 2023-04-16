<?php

class OpenGraphControllerTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        $_POST = [];
        Settings::set('og_image', '');
    }

    public function testSavePost(): void
    {
        $_POST['og_image'] = 'ogimage.jpg';

        $controller = new OpenGraphController();
        $controller->_savePost();

        $this->assertEquals('ogimage.jpg', Settings::get('og_image'));
    }
}
