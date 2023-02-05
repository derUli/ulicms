<?php

use UliCMS\Models\Content\Types\DefaultContentTypes;
use Spatie\Snapshots\MatchesSnapshots;

class DefaultContentTypesTest extends \PHPUnit\Framework\TestCase {

    use MatchesSnapshots;

    protected function setUp(): void {
        DefaultContentTypes::initTypes();
    }

    public function testGetAll() {
        $types = DefaultContentTypes::getAll();
        $this->assertGreaterThanOrEqual(11, count($types));
    }

    public function testGetExistingReturnsObject() {
        $typePage = DefaultContentTypes::get("page");
        $this->assertCount(13, $typePage->show);
        $this->assertContains(".menu-stuff", $typePage->show);
        $this->assertNotContains("#article-image", $typePage->show);

        $typeArticle = DefaultContentTypes::get("article");
        $this->assertCount(15, $typeArticle->show);
        $this->assertContains(".menu-stuff", $typeArticle->show);
        $this->assertContains("#article-image", $typeArticle->show);
    }

    public function testGetNonExistingReturnsNull() {
        $this->assertNull(DefaultContentTypes::get("gibts_nicht"));
    }

    public function testToJson() {
        $this->assertMatchesJsonSnapshot(DefaultContentTypes::toJSON());
    }

}
