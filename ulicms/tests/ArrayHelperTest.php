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
}
	