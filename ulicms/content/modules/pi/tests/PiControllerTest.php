<?php

class PiControllerTest extends \PHPUnit\Framework\TestCase {

    public function testRender() {
        $controller = ModuleHelper::getMainController("pi");
        $this->assertEquals("3,1415926535898", $controller->render());
    }

}
