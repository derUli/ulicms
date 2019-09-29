<?php

use UliCMS\CoreContent\UIUtils;

class UIUtilsTest extends \PHPUnit\Framework\TestCase {

    public function testGetRobotsListItems() {
        $items = UIUtils::getRobotsListItems();

        $this->assertCount(5, $items);

        $this->assertNull($items[0]->getValue());

        $countItems = 0;
        $countNoItems = 0;

        for ($i = 1; $i < count($items); $i++) {
            $this->assertNotEmpty($items[$i]->getValue());
            $this->assertNotEmpty($items[$i]->getText());
            $countNoItems += intval(str_contains("no", $items[$i]->getValue()));
            $countItems += intval(
                    !str_contains("no", $items[$i]->getValue()));
        }

        $this->assertEquals(1, $countItems);
        $this->assertEquals(3, $countNoItems);
    }

}
