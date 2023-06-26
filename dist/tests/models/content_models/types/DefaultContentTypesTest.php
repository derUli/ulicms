<?php

use App\Models\Content\Types\DefaultContentTypes;

class DefaultContentTypesTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        DefaultContentTypes::initTypes();
    }

    public function testGetAll(): void {
        $types = DefaultContentTypes::getAll();
        $this->assertGreaterThanOrEqual(11, count($types));
    }

    public function testGetExistingReturnsObject(): void {
        $typePage = DefaultContentTypes::get('page');
        $this->assertCount(13, $typePage->show);
        $this->assertContains('.menu-stuff', $typePage->show);
        $this->assertNotContains('#article-image', $typePage->show);

        $typeArticle = DefaultContentTypes::get('article');
        $this->assertCount(15, $typeArticle->show);
        $this->assertContains('.menu-stuff', $typeArticle->show);
        $this->assertContains('#article-image', $typeArticle->show);
    }

    public function testGetNonExistingReturnsNull(): void {
        $this->assertNull(DefaultContentTypes::get('gibts_nicht'));
    }

    public function testToJson(): void {
        $this->assertEquals(
            file_get_contents(
                \App\Utils\Path::resolve(
                    'ULICMS_ROOT/tests/fixtures/json' .
                    '/defaultContentTypes.json'
                )
            ),
            DefaultContentTypes::toJSON()
        );
    }
}
