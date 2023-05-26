<?php

use App\Constants\CommentStatus;

class CommentStatusTest extends \PHPUnit\Framework\TestCase {
    public function testDefault(): void {
        $this->assertEquals('pending', CommentStatus::DEFAULT_STATUS);
    }

    public function testPending(): void {
        $this->assertEquals('pending', CommentStatus::PENDING);
    }

    public function testPublished(): void {
        $this->assertEquals('published', CommentStatus::PUBLISHED);
    }

    public function testSpam(): void {
        $this->assertEquals('spam', CommentStatus::SPAM);
    }
}
