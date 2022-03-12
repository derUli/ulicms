<?php

use UliCMS\Helpers\ArrayHelper;

class ArrayHelperTest extends \PHPUnit\Framework\TestCase {

    public function testTake4String() {
        $this->assertEquals("abcd", ArrayHelper::take(4, "abcdefghijklmnopqrstuvwxyz"));
    }

    public function testTake3Array() {
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

    public function testTake3Invalid() {
        $this->assertNull(ArrayHelper::take(4, 2017));
    }

    public function testInsertBeforeReturnsArray() {
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

    public function testInsertBeforeReturnsThrowsException() {
        $input = array(
            "apple",
            "tomato",
            "banana",
            "cucumber"
        );

        $this->expectException("Exception");
        $this->expectExceptionMessage("Index not found");

        ArrayHelper::insertBefore($input, PHP_INT_MAX, 'gibts_nicht');
    }

    public function testInsertAfterReturnsArray() {
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

    public function testInsertAfterReturnsThrowsException() {
        $input = array(
            "apple",
            "tomato",
            "banana",
            "cucumber"
        );

        $this->expectException("Exception");
        $this->expectExceptionMessage("Index not found");

        ArrayHelper::insertAfter($input, PHP_INT_MAX, 'gibts_nicht');
    }

    public function testIsSingleWithEmptyArray() {
        $this->assertFalse(ArrayHelper::isSingle([]));
    }

    public function testIsSingleWithOneItem() {
        $this->assertTrue(ArrayHelper::isSingle(array(
                    "foo"
        )));
    }

    public function testIsSingleWithTwoItems() {
        $this->assertFalse(ArrayHelper::isSingle(array(
                    "foo",
                    "bar"
        )));
    }

    public function testGetSingleWithEmptyArray() {
        $this->assertNull(ArrayHelper::getSingle([]));
    }

    public function testGetSingleWithOneItem() {
        $this->assertEquals("foo", ArrayHelper::getSingle(array(
                    "foo"
        )));
    }

    public function testGetSingleWithTwoItems() {
        $this->assertNull(ArrayHelper::getSingle(array(
                    "foo",
                    "bar"
        )));
    }

    private function getNestesdArray() {
        return [
            "foo",
            "bar",
            [
                "hello",
                "world",
                [
                    "apache",
                    "php",
                    "mysql",
                    "linux"]
            ]
        ];
    }

    public function testFlattenWithNestedArray() {
        $input = $this->getNestesdArray();
        $expected = [
            "foo",
            "bar",
            "hello",
            "world",
            "apache",
            "php",
            "mysql",
            "linux"
        ];
        $this->assertEquals($expected, ArrayHelper::flatten($input));
    }

    public function testFlattenWithFlattenArray() {
        $input = ["dog", "cat", "pig", "horse"];
        $this->assertEquals(
                $input,
                ArrayHelper::flatten($input)
        );
    }

    public function testFlattenWithString() {
        $this->assertEquals(
                ["ulicms"],
                ArrayHelper::flatten("ulicms")
        );
    }

    public function testGetValueOrDefaultWithNullReturnsDefault() {
        $this->assertEquals(
                "foobar",
                ArrayHelper::getValueOrDefault(null, "hello", "foobar")
        );
    }

    public function testGetValueOrDefaultWithArrayReturnsDefault() {
        $this->assertEquals(
                "foobar",
                ArrayHelper::getValueOrDefault(
                        ["gibts" => "not"],
                        "hello",
                        "foobar"
                )
        );
    }

    public function testGetValueOrDefaultWithArrayReturnsValue() {
        $this->assertEquals(
                "world",
                ArrayHelper::getValueOrDefault(
                        ["hello" => "world"],
                        "hello",
                        "foobar"
                )
        );
    }

    private function getArrayTestData(): array {
        return [
            "foo" => "bar",
            "hello" => "world",
            "fire" => "water",
            "metal" => "rock"
        ];
    }

    public function testArrayHasMultipleKeysReturnsTrue() {
        $this->assertTrue(
                ArrayHelper::hasMultipleKeys(
                        $this->getArrayTestData(),
                        [
                            "foo",
                            "fire"
                        ]
                )
        );
    }

    public function testArrayHasMultipleKeysReturnsFalse() {
        $this->assertFalse(
                ArrayHelper::hasMultipleKeys(
                        $this->getArrayTestData(),
                        [
                            "foo",
                            "fire",
                            "nope"
                        ]
                )
        );

        $this->assertFalse(
                ArrayHelper::hasMultipleKeys(
                        null,
                        [
                            "foo",
                            "fire",
                            "nope"
                        ]
                )
        );
    }

}
