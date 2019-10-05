<?php

use UliCMS\Packages\PatchManager;

class PatchManagerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        for ($i = 1; $i <= 3; $i++) {
            Database::pQuery(
                    "insert into {prefix}installed_patches "
                    . "(name, description, url, date) VALUES "
                    . "(?,?,?, NOW())",
                    [
                        "patch-$i",
                        "Beschreibung $i",
                        "https://google.de",
                    ],
                    true
            );
        }
    }

    public function tearDown() {
        $patchManager = new PatchManager();
        $patchManager->truncateInstalledPatches();
    }

    public function testGetInstalledPatches() {

        $patchManager = new PatchManager();
        $patches = $patchManager->getInstalledPatches();

        $this->assertIsArray($patches);

        $this->assertCount(3, $patches);

        foreach ($patches as $patch) {
            $this->assertNotEmpty($patch->id);
            $this->assertNotEmpty($patch->name);
            $this->assertNotEmpty($patch->description);
            $this->assertNotEmpty($patch->url);
            $this->assertNotEmpty($patch->date);
        }
    }

    public function testTruncateInstalledPatches() {
        $patchManager = new PatchManager();
        $patchManager->truncateInstalledPatches();
        $query = Database::selectAll("installed_patches");
        $this->assertFalse(Database::any($query));
    }

    public function testGetInstalledPatchNames() {
        for ($i = 1; $i <= 3; $i++) {
            Database::pQuery(
                    "insert into {prefix}installed_patches "
                    . "(name, description, url, date) VALUES "
                    . "(?,?,?, NOW())",
                    [
                        "patch-$i",
                        "Beschreibung $i",
                        "https://google.de",
                    ],
                    true
            );
        }
        $patchManager = new PatchManager();
        $patches = $patchManager->GetInstalledPatchNames();

        $this->assertIsArray($patches);

        foreach ($patches as $patch) {
            $this->assertNotEmpty($patch);
        }
    }

}
