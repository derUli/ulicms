<?php

use UliCMS\Models\Content\Types\DefaultContentTypes;

class DefaultContentTypesTest extends \PHPUnit\Framework\TestCase {

	public function setUp() {
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
		$this->assertEquals(
				file_get_contents(
						Path::resolve(
								"ULICMS_ROOT/tests/fixtures/json" .
								"/defaultContentTypes.json"
						)
				),
				DefaultContentTypes::toJSON());
	}

}
