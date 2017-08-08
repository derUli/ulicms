<?php

class PathTest extends PHPUnit_Framework_TestCase
{

    public function testGetPathByIsoCode()
    {
        $controller = ModuleHelper::getMainController("gosquared_flags");
        $this->assertEquals("content/modules/gosquared_flags/flags-iso/flat/64/FR.png", $controller->getPathByIsoCode("FR.png", 64));
        $this->assertEquals("content/modules/gosquared_flags/flags-iso/shiny/32/FR.png", $controller->getPathByIsoCode("FR.png", 32, "shiny"));
        $this->assertNull($controller->getPathByIsoCode("LP.png", 32, "shiny"));
    }

    public function testGetPathByCoutryName()
    {
        $controller = ModuleHelper::getMainController("gosquared_flags");
        $this->assertEquals("content/modules/gosquared_flags/flags/flat/64/Germany.png", $controller->getPathByCountryName("Germany.png", 64));
        $this->assertEquals("content/modules/gosquared_flags/flags/shiny/32/Germany.png", $controller->getPathByCountryName("Germany.png", 32, "shiny"));
        $this->assertNull($controller->getPathByCountryName("Lampukistan.png", 32, "shiny"));
    }
}