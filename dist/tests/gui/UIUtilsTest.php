<?php

use App\CoreContent\UIUtils;

class UIUtilsTest extends \PHPUnit\Framework\TestCase {
    public function testGetRobotsListItems(): void {
        $items = UIUtils::getRobotsListItems();

        $this->assertCount(5, $items);

        $this->assertNull($items[0]->getValue());

        $countItems = 0;
        $countNoItems = 0;

        $itemCount = count($items);
        for ($i = 1; $i < $itemCount; $i++) {
            $this->assertNotEmpty($items[$i]->getValue());
            $this->assertNotEmpty($items[$i]->getText());
            $countNoItems += str_contains($items[$i]->getValue(), 'no');
            $countItems += ! str_contains($items[$i]->getValue(), 'no');
        }

        $this->assertEquals(1, $countItems);
        $this->assertEquals(3, $countNoItems);
    }
}
