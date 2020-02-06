<?php

use UliCMS\CoreContent\PageTableRenderer;

class PageTableRendererTest extends \PHPUnit\Framework\TestCase {

    public function testGetDataReturns3Items() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $renderer = new PageTableRenderer($user);
        $data = $renderer->getData(0, 3, 123);

        $this->assertEquals(123, $data["draw"]);
        $this->assertCount(3, $data["data"]);

        $this->assertEquals($data["recordsTotal"], $data["recordsFiltered"]);
    }

    public function testGetDataReturnsOther3Items() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $renderer = new PageTableRenderer($user);
        $data1 = $renderer->getData(0, 3, 124);
        $data2 = $renderer->getData(4, 3, 123);

        $this->assertCount(3, $data1["data"]);
        $this->assertCount(3, $data2["data"]);

        for ($i = 0; $i < count($data2["data"]); $i++) {
            $this->assertNotEquals(
                    $data1["data"][$i][0],
                    $data2["data"][$i][0]
            );
        }

        $this->assertEquals($data2["recordsTotal"], $data2["recordsFiltered"]);
    }

    public function testGetDataFiltered() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $renderer = new PageTableRenderer($user);
        $data = $renderer->getData(0, 10, 123, "lorem");
        $this->assertLessThan($data["recordsTotal"],
                $data["recordsFiltered"]);

        foreach ($data["data"] as $dataset) {
            $this->assertStringContainsStringIgnoringCase("lorem", $dataset[0]);
        }
    }

    public function testGetDataFilteredWithStart() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $renderer = new PageTableRenderer($user);
        $data = $renderer->getData(1, 1, 123, "lorem");
        $this->assertLessThan($data["recordsTotal"],
                $data["recordsFiltered"]);

        $this->assertCount(1, $data["data"]);

        $this->assertGreaterThan(1,
                $data["recordsFiltered"]);

        foreach ($data["data"] as $dataset) {
            $this->assertStringContainsStringIgnoringCase("lorem", $dataset[0]);
        }
    }

}
