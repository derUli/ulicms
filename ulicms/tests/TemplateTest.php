<?php
use UliCMS\Exceptions\FileNotFoundException;

class TemplateTest extends \PHPUnit\Framework\TestCase
{

    public function testRenderPartialSuccess()
    {
        $this->assertEquals("Hello World!", Template::renderPartial("hello", "impro17"));
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