<?php

use UliCMS\CoreContent\PageTableRenderer;

class PageTableRendererTest extends \PHPUnit\Framework\TestCase {
public function setUp(){
    include_once getLanguageFilePath("en");
}
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

    public function testGetDataFilterByLanguageAndType() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $renderer = new PageTableRenderer($user);

        $filters = [
            "type" => "module"
        ];

        $withoutLanguageFilter = $renderer->getData(0, 20, 123, "", $filters);

        $filters["language"] = "de";
        $data = $renderer->getData(0, 20, 123, "", $filters);

        $this->assertGreaterThanOrEqual(1, count($data["data"]));
        $this->assertGreaterThan(
                count($data["data"]),
                count($withoutLanguageFilter["data"])
        );
    }
    
    
    public function testGetDataFilterByParentIdNoParent() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $renderer = new PageTableRenderer($user);

        $data = $renderer->getData(0,
                20,
                123,
                "",
                [
                    "parent_id" => 0
                ]
        );
        
        foreach($data["data"] as $dataset){
            $this->assertEquals("[None]", $dataset[3]);
        }
    }
        
    public function testGetDataFilterByParentIdWithParent() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $renderer = new PageTableRenderer($user);
        
        $parentPage = ContentFactory::getBySlugAndLanguage("modules", "en");
        
        $data = $renderer->getData(0,
                20,
                123,
                "",
                [
                    "parent_id" => $parentPage->getID()
                ]
        );
        
        
        $this->assertGreaterThanOrEqual(2, count($data["data"]));
        
        foreach($data["data"] as $dataset){
            $this->assertEquals("Modules", $dataset[3]);
        }
    }

    public function testGetDataFilterByCategoryId() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $renderer = new PageTableRenderer($user);

        $categoryGeneralData = $renderer->getData(0,
                20,
                123,
                "",
                [
                    "category_id" => 1
                ]
        );
        $nonExistingCategory = $renderer->getData(0,
                20,
                123,
                "",
                [
                    "category_id" => PHP_INT_MAX
                ]
        );
        $this->assertGreaterThanOrEqual(1, count($categoryGeneralData["data"]));
        $this->assertCount(0, $nonExistingCategory["data"]);
    }

    public function testGetDataFilterByApproved() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $renderer = new PageTableRenderer($user);

        $approvedData = $renderer->getData(0,
                20,
                123,
                "",
                [
                    "approved" => 1
                ]
        );
        $notApprovedData = $renderer->getData(0,
                20,
                123,
                "",
                [
                    "approved" => 0
                ]
        );
        $this->assertGreaterThanOrEqual(1, count($approvedData["data"]));
        $this->assertNotEquals(count($approvedData["data"]), $notApprovedData["data"]);
    }

    public function testGetDataFilterByMenu() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $renderer = new PageTableRenderer($user);

        $data = $renderer->getData(0,
                20,
                123,
                "",
                [
                    "menu" => "top"
                ]
        );

        foreach ($data["data"] as $dataset) {
            $this->assertEquals("Top", $dataset[1]);
        }
    }

    public function testGetDataFilterActive() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $renderer = new PageTableRenderer($user);

        $dataActive = $renderer->getData(0,
                20,
                123,
                "",
                [
                    "active" => 1
                ]
        );

        foreach ($dataActive["data"] as $dataset) {
            $this->assertEquals("Yes", $dataset[4]);
        }

        $dataInactive = $renderer->getData(0,
                20,
                123,
                "",
                [
                    "active" => 0
                ]
        );

        foreach ($dataInactive["data"] as $dataset) {
            $this->assertEquals("No", $dataset[4]);
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
