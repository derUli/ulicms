<?php
use UliCMS\Exceptions\NotImplementedException;

class CommentsControllerTest extends \PHPUnit\Framework\TestCase
{

    private $initialCommentsMustBeApproved;

    public function setUp()
    {
        $this->initialCommentsMustBeApproved = Settings::get("comments_must_be_approved");
    }

    public function tearDown()
    {
        if (boolval($this->initialCommentsMustBeApproved)) {
            Settings::set("comments_must_be_approved", "1");
        } else {
            Settings::delete("comments_must_be_approved");
        }
    }

    public function testGetDefaultStatusExpectPending()
    {
        $controller = new CommentsController();
        Settings::set("comments_must_be_approved", "1");
        $this->assertEquals(CommentStatus::PENDING, $controller->getDefaultStatus());
    }

    public function testGetDefaultStatusExpectPublished()
    {
        Settings::delete("comments_must_be_approved");
        
        $controller = new CommentsController();
        $this->assertEquals(CommentStatus::PUBLISHED, $controller->getDefaultStatus());
    }

    public function testGetResults()
    {
        throw new NotImplementedException();
    }
}