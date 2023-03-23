<?php


class MenuTest extends \PHPUnit\Framework\TestCase
{
    public function testGetAllUsedMenus()
    {
        $menus = get_all_used_menus();
        $this->assertCount(2, $menus);
        $this->isTrue(in_array("top", $menus));
        $this->isFalse(in_array("left", $menus));
    }

    public function testGetAllMenus()
    {
        $menus = get_all_menus();
        $this->assertContains("top", $menus);
        $this->assertContains("not_in_menu", $menus);
        $this->assertNotContains("foo", $menus);
        $this->assertNotContains("bar", $menus);
    }

    public function testGetAllMenusOnlyUsed()
    {
        $menus = get_all_menus(true);
        $this->assertContains("top", $menus);
        $this->assertContains("not_in_menu", $menus);
        $this->assertNotContains("foo", $menus);
        $this->assertNotContains("bar", $menus);
    }
}
