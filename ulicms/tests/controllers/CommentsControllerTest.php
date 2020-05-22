<?php

use UliCMS\Constants\CommentStatus;

class CommentsControllerTest extends \PHPUnit\Framework\TestCase {

    private $initialCommentsMustBeApproved;
    private $initialCommentsDefaultLimit;

    public function setUp() {
        $this->initialCommentsMustBeApproved = Settings::get(
                        "comments_must_be_approved"
        );
        $this->initialCommentsDefaultLimit = Settings::get(
                        "comments_default_limit"
        );
    }

    public function tearDown() {
        if (boolval($this->initialCommentsMustBeApproved)) {
            Settings::set("comments_must_be_approved", "1");
        } else {
            Settings::delete("comments_must_be_approved");
        }

        Settings::set(
                "comments_default_limit",
                $this->initialCommentsDefaultLimit
        );
    }

    public function testGetDefaultStatusExpectPending() {
        $controller = new CommentsController();
        Settings::set("comments_must_be_approved", "1");
        $this->assertEquals(
                CommentStatus::PENDING,
                $controller->_getDefaultStatus()
        );
    }

    public function testGetDefaultStatusExpectPublished() {
        Settings::delete("comments_must_be_approved");

        $controller = new CommentsController();
        $this->assertEquals(
                CommentStatus::PUBLISHED,
                $controller->_getDefaultStatus()
        );
    }

    public function testGetResultsNoResults() {
        $controller = new CommentsController();
        $this->assertCount(
                0,
                $controller->_getResults(
                        CommentStatus::SPAM,
                        PHP_INT_MAX
                )
        );
        $this->assertCount(
                0,
                $controller->_getResults(
                        null,
                        PHP_INT_MAX
                )
        );
        $this->assertCount(
                0,
                $controller->_getResults(
                        null,
                        null,
                        null
                )
        );
    }

    public function testGetDefaultLimit() {
        Settings::set("comments_default_limit", "123");

        $controller = new CommentsController();
        $this->assertEquals(123, $controller->_getDefaultLimit());
    }

}
