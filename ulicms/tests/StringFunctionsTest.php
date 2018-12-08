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

    public function testUnhtmlspecialchars()
    {
        $input = '&lt;span style=&quot;color:red&quot;&gt;This is HTML&lt;/span&gt;';
        $expected = '<span style="color:red">This is HTML</span>';
        $this->assertEquals($expected, unhtmlspecialchars($input));
    }

    public function testNormalizeLn()
    {
        $input = "Line1\nLine2\rLine3\r\n";
        $this->assertEquals("Line1\r\nLine2\r\nLine3\r\n", normalizeLN($input));
        $this->assertEquals("Line1\r\nLine2\r\nLine3\r\n", normalizeLN($input, "\r\n"));
        $this->assertEquals("Line1\rLine2\rLine3\r", normalizeLN($input, "\r"));
        $this->assertEquals("Line1\nLine2\nLine3\n", normalizeLN($input, "\n"));
    }

    public function testMultiExplode()
    {
        $input = "FooTrenn1BarTrenn2LoremTrenn1Ipsum";
        $result = multi_explode(array(
            "Trenn1",
            "Trenn2"
        ), $input);
        
        $this->assertEquals("Foo", $result[0]);
        $this->assertEquals("Bar", $result[1]);
        $this->assertEquals("Lorem", $result[2]);
        $this->assertEquals("Ipsum", $result[3]);
    }

    public function testKeywordsFromString()
    {
        $input = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.";
        
        $keywords = keywordsFromString($input);
        
        $this->assertCount(40, $keywords);
        
        $this->assertEquals(12, $keywords["et"]);
        $this->assertEquals(6, $keywords["Lorem"]);
        $this->assertEquals(3, $keywords["dolores"]);
    }

    public function testStrReplaceFirst()
    {
        $input = "Apple Strawberry Apple Strawberry";
        $this->assertEquals("Banana Strawberry Apple Strawberry", str_replace_first("Apple", "Banana", $input));
        $this->assertEquals("Apple Tomato Apple Strawberry", str_replace_first("Strawberry", "Tomato", $input));
        $this->assertEquals($input, str_replace_first("Cucumber", "Tomato", $input));
    }

    public function testStrReplaceNth()
    {
        $input = "Apple Strawberry Apple Strawberry";
        $this->assertEquals("Apple Tomato Apple Strawberry", str_replace_nth("Strawberry", "Tomato", $input, 0));
        $this->assertEquals("Apple Strawberry Apple Tomato", str_replace_nth("Strawberry", "Tomato", $input, 1));
    }

    public function testMbStrSplit()
    {
        $input = "Dieser Hügel ist der höchste.";
        $result = mb_str_split($input);
        $this->assertCount(29, mb_str_split($input));
        $this->assertEquals("ü", $result[8]);
        $this->assertEquals("i", $result[1]);
        $this->assertEquals("ö", $result[22]);
    }
}