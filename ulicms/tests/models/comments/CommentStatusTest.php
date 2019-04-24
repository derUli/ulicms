<?php

class CommentStatusTest extends \PHPUnit\Framework\TestCase {

    public function testDefault() {
        $this->assertEquals("pending", CommentStatus::DEFAULT_STATUS);
    }

    public function testPending() {
        $this->assertEquals("pending", CommentStatus::PENDING);
    }

    public function testPublished() {
        $this->assertEquals("published", CommentStatus::PUBLISHED);
    }

    public function testSpam() {
        $this->assertEquals("spam", CommentStatus::SPAM);
    }

}
