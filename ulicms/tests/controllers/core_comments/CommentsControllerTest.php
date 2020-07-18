<?php

use UliCMS\Constants\CommentStatus;
use UliCMS\Models\Content\Comment;
use UliCMS\Exceptions\NotImplementedException;

class CommentsControllerTest extends \PHPUnit\Framework\TestCase
{
    private $initialCommentsMustBeApproved;
    private $initialCommentsDefaultLimit;

    protected function setUp(): void
    {
        $this->initialCommentsMustBeApproved = Settings::get(
            "comments_must_be_approved"
        );
        $this->initialCommentsDefaultLimit = Settings::get(
            "comments_default_limit"
        );
    }

    protected function tearDown(): void
    {
        Database::deleteFrom("comments", "text like 'Unit Test%'");

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

    public function testGetDefaultStatusExpectPending()
    {
        $controller = new CommentsController();
        Settings::set("comments_must_be_approved", "1");
        $this->assertEquals(
            CommentStatus::PENDING,
            $controller->_getDefaultStatus()
        );
    }

    public function testGetDefaultStatusExpectPublished()
    {
        Settings::delete("comments_must_be_approved");

        $controller = new CommentsController();
        $this->assertEquals(
            CommentStatus::PUBLISHED,
            $controller->_getDefaultStatus()
        );
    }

    public function testGetResultsNoResults()
    {
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

    public function testGetDefaultLimit()
    {
        Settings::set("comments_default_limit", "123");

        $controller = new CommentsController();
        $this->assertEquals(123, $controller->_getDefaultLimit());
    }

    public function testGetCommentTextReturnstext()
    {
        $page = $this->getTestComment();
        $controller = new CommentsController();
        $commentText = $controller->_getCommentText($page->getID());

        $this->assertEquals(
            "Unit Test 1<br />\n" .
                '<a href="https://google.com" rel="nofollow" target="_blank">'
                . 'https://google.com'
                . '</a>',
            $commentText
        );
    }

    public function testGetCommentTextReturnsNull()
    {
        $controller = new CommentsController();
        $commentText = $controller->_getCommentText(PHP_INT_MAX);

        $this->assertNull($commentText);
    }

    protected function getTestComment(): Comment
    {
        $content = ContentFactory::getAll();
        $first = $content[0];
        $comment = new Comment();
        $comment->setContentId($first->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setStatus(CommentStatus::PUBLISHED);
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 1\nhttps://google.com");
        $comment->setRead(true);

        $time = time();
        $comment->setDate($time);

        $comment->save();

        return $comment;
    }

    public function testFilterComments()
    {
        $page = $this->getTestComment();
        $controller = new CommentsController();

        $comments = $controller->_filterComments(
            CommentStatus::PUBLISHED,
            $page->getContentId()
        );

        $this->assertCount(1, $comments);
    }

    public function testDoActionThrowsException()
    {
        $controller = new CommentsController();

        $this->expectException(NotImplementedException::class);
        $comment = new Comment();
        $controller->_doAction($comment, "do_magic");
    }

    public function testDoActionPublish()
    {
        $controller = new CommentsController();

        $comment = $this->getTestComment();
        $updatedComment = $controller->_doAction($comment, "publish");

        $this->assertEquals(CommentStatus::PUBLISHED, $updatedComment->getStatus());
        $this->assertTrue($updatedComment->isRead());
    }

    public function testDoActionUnpublish()
    {
        $controller = new CommentsController();

        $comment = $this->getTestComment();
        $updatedComment = $controller->_doAction($comment, "unpublish");

        $this->assertEquals(CommentStatus::PENDING, $updatedComment->getStatus());
    }

    public function testDoActionMarkAsSpam()
    {
        $controller = new CommentsController();

        $comment = $this->getTestComment();
        $updatedComment = $controller->_doAction($comment, "mark_as_spam");

        $this->assertEquals(CommentStatus::SPAM, $updatedComment->getStatus());
    }

    public function testDoActionMarkAsRead()
    {
        $controller = new CommentsController();

        $comment = $this->getTestComment();
        $updatedComment = $controller->_doAction($comment, "mark_as_read");

        $this->assertTrue($updatedComment->isRead());
    }

    public function testDoActionsMarkAsRead()
    {
        $controller = new CommentsController();

        $commentIds = [
            $this->getTestComment()->getId(),
            $this->getTestComment()->getId(),
        ];
        $updatedComments = $controller->_doActions($commentIds, "mark_as_read");
        $this->assertCount(2, $updatedComments);

        foreach ($updatedComments as $comment) {
            $this->assertTrue($comment->isRead());
        }
    }

    public function testDoActionMarkAsUnread()
    {
        $controller = new CommentsController();

        $comment = $this->getTestComment();
        $updatedComment = $controller->_doAction($comment, "mark_as_unread");

        $this->assertFalse($updatedComment->isRead());
    }

    public function testDoActionDelete()
    {
        $controller = new CommentsController();

        $comment = $this->getTestComment();
        $updatedComment = $controller->_doAction($comment, "delete");

        $this->assertFalse($updatedComment->isPersistent());
    }
}
