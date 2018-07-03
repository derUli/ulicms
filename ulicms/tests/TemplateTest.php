<?php

class TemplateTest extends PHPUnit_Framework_TestCase
{

    public function testRenderPartialSuccess()
    {
        throw new NotImplementedException("Test not implemented yet");
    }

    public function testRenderPartialNotFound()
    {
        try {
            $nothing = Template::renderPartial("nothing", "impro17");
            $this->fail("FileNotFoundException not thrown");
        } catch (FileNotFoundException $e) {
            $this->assertNotNull("Partial not found test successfull");
        }
    }
}