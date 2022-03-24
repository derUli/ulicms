<?php

use UliCMS\Constants\AllowedTags;

class AllowedTagsTest extends \PHPUnit\Framework\TestCase {

    public function testAllowedTags() {
        $this->assertEquals(103, substr_count(AllowedTags::HTML5_ALLOWED_TAGS, "<"));
        $this->assertEquals(103, substr_count(AllowedTags::HTML5_ALLOWED_TAGS, ">"));
        $this->assertStringContainsString("<p>", AllowedTags::HTML5_ALLOWED_TAGS);
        $this->assertStringContainsString("<div>", AllowedTags::HTML5_ALLOWED_TAGS);
    }

}
