<?php

class AllowedTagsTest extends \PHPUnit\Framework\TestCase {

    public function testAllowedTags() {
        $this->assertEquals(103, substr_count(HTML5_ALLOWED_TAGS, "<"));
        $this->assertEquals(103, substr_count(HTML5_ALLOWED_TAGS, ">"));
        $this->assertStringContainsString("<p>", HTML5_ALLOWED_TAGS);
        $this->assertStringContainsString("<div>", HTML5_ALLOWED_TAGS);
    }

}
