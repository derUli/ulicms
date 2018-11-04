<?php

class ArrayHelperTest extends \PHPUnit\Framework\TestCase
{

    public function testTake4String()
    {
        $this->assertEquals("abcd", ArrayHelper::take(4, "abcdefghijklmnopqrstuvwxyz"));
    }

    public function testTake3Array()
    {
        $this->assertEquals(array(
            "cat",
            "dog",
            "porcupine"
        ), ArrayHelper::take(3, array(
            "cat",
            "dog",
            "porcupine",
            "eagle",
            "dolphin",
            "ape"
        )));
    }

    public function testTake3Invalid()
    {
        $this->assertNull(ArrayHelper::take(4, 2017));
    }

    public function testInsertBefore()
    {
        $input = array(
            "apple",
            "tomato",
            "banana",
            "cucumber"
        );
        
        $this->assertEquals(array(
            "apple",
            "tomato",
            "banana",
            "pineapple",
            "cucumber"
        ), ArrayHelper::insertBefore($input, 3, "pineapple"));
        
        $this->assertEquals(array(
            "pineapple",
            "apple",
            "tomato",
            "banana",
            "cucumber"
        ), ArrayHelper::insertBefore($input, 0, "pineapple"));
    }

    public function testAfterBefore()
    {
        $input = array(
            "apple",
            "tomato",
            "banana",
            "cucumber"
        );
        
        $this->assertEquals(array(
            "apple",
            "pineapple",
            "tomato",
            "banana",
            "cucumber"
        ), ArrayHelper::insertAfter($input, 0, "pineapple"));
        
        $this->assertEquals(array(
            "apple",
            "tomato",
            "banana",
            "cucumber",
            "pineapple"
        ), ArrayHelper::insertAfter($input, 3, "pineapple"));
    }
}
	