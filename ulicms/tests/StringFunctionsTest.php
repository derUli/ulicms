<?php

class StringFunctionsTest extends \PHPUnit\Framework\TestCase
{

    public function testStrBoolTrue()
    {
        $this->assertEquals("true", strbool(true));
        $this->assertEquals("true", strbool(1));
    }

    public function testStrBoolFalse()
    {
        $this->assertEquals("false", strbool(false));
        $this->assertEquals("false", strbool(0));
        $this->assertEquals("false", strbool(null));
    }

    public function testConvertLineEndingsToLN()
    {
        $input = "Line1\r\nLine2\rLine3\n";
        $expected = "Line1\nLine2\nLine3\n";
        $this->assertEquals($expected, convertLineEndingsToLN($input));
    }

    public function testBr2nlr()
    {
        $input = "Line1<br>Line2<br />Line3<br/>";
        $expected = "Line1\r\nLine2\r\nLine3\r\n";
        
        $this->assertEquals($expected, br2nlr($input));
    }

    public function testSanitize()
    {
        $input = array(
            "My\r\nWorld\r",
            " entferne%0adas",
            " entferne%0adas",
            "%0dlorem ipsum %0d"
        );
        
        sanitize($input);
        $this->assertCount(4, $input);
        $this->assertEquals("MyWorld", $input[0]);
        $this->assertEquals(" entfernedas", $input[1]);
        $this->assertEquals(" entfernedas", $input[2]);
        $this->assertEquals("lorem ipsum ", $input[3]);
    }
}