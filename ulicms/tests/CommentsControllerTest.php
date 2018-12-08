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

    public function testGetResultsWithoutArguments()
    {
        $controller = new CommentsController();
        throw new NotImplementedException();
    }

    public function testGetResultsWithStatus()
    {
        $controller = new CommentsController();
        throw new NotImplementedException();
    }

    public function testGetResultsWithStatusAndContentId()
    {
        $controller = new CommentsController();
        throw new NotImplementedException();
    }

    public function testGetResultsWithStatusAndContentIdAndTake0()
    {
        $controller = new CommentsController();
        throw new NotImplementedException();
    }

    public function testGetResultsWithStatusAndContentIdAndTake3()
    {
        $controller = new CommentsController();
        throw new NotImplementedException();
    }

    public function testGetResultsNoResults()
    {
        $controller = new CommentsController();
        $this->assertCount(0, $controller->getResults(CommentStatus::SPAM, PHP_INT_MAX));
    }
}