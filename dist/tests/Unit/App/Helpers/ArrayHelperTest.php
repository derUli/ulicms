<?php

use App\Helpers\ArrayHelper;

class ArrayHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testInsertBeforeReturnsArray()
    {
        $input = [
            'apple',
            'tomato',
            'banana',
            'cucumber'
        ];

        $this->assertEquals([
            'apple',
            'tomato',
            'banana',
            'pineapple',
            'cucumber'
        ], ArrayHelper::insertBefore($input, 3, 'pineapple'));

        $this->assertEquals([
            'pineapple',
            'apple',
            'tomato',
            'banana',
            'cucumber'
        ], ArrayHelper::insertBefore($input, 0, 'pineapple'));
    }

    public function testInsertBeforeReturnsThrowsException()
    {
        $input = [
            'apple',
            'tomato',
            'banana',
            'cucumber'
        ];

        $this->expectException('Exception');
        $this->expectExceptionMessage('Index not found');

        ArrayHelper::insertBefore($input, PHP_INT_MAX, 'gibts_nicht');
    }

    public function testInsertAfterReturnsArray()
    {
        $input = [
            'apple',
            'tomato',
            'banana',
            'cucumber'
        ];

        $this->assertEquals([
            'apple',
            'pineapple',
            'tomato',
            'banana',
            'cucumber'
        ], ArrayHelper::insertAfter($input, 0, 'pineapple'));

        $this->assertEquals([
            'apple',
            'tomato',
            'banana',
            'cucumber',
            'pineapple'
        ], ArrayHelper::insertAfter($input, 3, 'pineapple'));
    }

    public function testInsertAfterReturnsThrowsException()
    {
        $input = [
            'apple',
            'tomato',
            'banana',
            'cucumber'
        ];

        $this->expectException('Exception');
        $this->expectExceptionMessage('Index not found');

        ArrayHelper::insertAfter($input, PHP_INT_MAX, 'gibts_nicht');
    }

    public function testArrayHasMultipleKeysReturnsTrue()
    {
        $this->assertTrue(
            ArrayHelper::hasMultipleKeys(
                $this->getArrayTestData(),
                [
                    'foo',
                    'fire'
                ]
            )
        );
    }

    public function testArrayHasMultipleKeysReturnsFalse()
    {
        $this->assertFalse(
            ArrayHelper::hasMultipleKeys(
                $this->getArrayTestData(),
                [
                    'foo',
                    'fire',
                    'nope'
                ]
            )
        );

        $this->assertFalse(
            ArrayHelper::hasMultipleKeys(
                null,
                [
                    'foo',
                    'fire',
                    'nope'
                ]
            )
        );
    }

    private function getNestesdArray()
    {
        return [
            'foo',
            'bar',
            [
                'hello',
                'world',
                [
                    'apache',
                    'php',
                    'mysql',
                    'linux']
            ]
        ];
    }

    private function getArrayTestData(): array
    {
        return [
            'foo' => 'bar',
            'hello' => 'world',
            'fire' => 'water',
            'metal' => 'rock'
        ];
    }
}
