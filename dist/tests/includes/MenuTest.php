<?php


class MenuTest extends \PHPUnit\Framework\TestCase {
    public function testGetAllUsedMenus() {
        $menus = get_all_used_menus();
        $this->assertTrue(in_array('top', $menus));
    }

    public function testGetAllMenus() {
        $menus = get_all_menus();
        $this->assertCount(2, $menus);

        $this->assertContains('top', $menus);
        $this->assertContains('not_in_menu', $menus);
    }

    public function testGetAllMenusOnlyUsed() {
        $menus = get_all_menus(true);
        $this->assertContains('top', $menus);
    }
}
