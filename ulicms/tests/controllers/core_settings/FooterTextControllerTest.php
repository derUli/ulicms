<?php

class FooterTextControllerTest extends \PHPUnit\Framework\TestCase {

    protected function tearDown(): void {
        $_POST = [];
        Settings::set("footer_text", "");
    }

    public function testSavePost(): void {
        $_POST["footer_text"] = "(C) 2020 by John Doe";
        $controller = new FooterTextController();
        $controller->_savePost();

        $this->assertEquals(
                "(C) 2020 by John Doe",
                Settings::get('footer_text')
        );
    }

}
