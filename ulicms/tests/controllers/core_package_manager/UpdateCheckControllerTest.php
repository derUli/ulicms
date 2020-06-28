<?php

class UpdateCheckControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testGetPatchCheck()
    {
        $controller = new UpdateCheckController();
        $html = $controller->_patchCheck();
        $this->assertIsString($html);
    }
}
