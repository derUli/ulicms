<?php

use UliCMS\Constants\CommentStatus;
use UliCMS\Models\Content\Comment;

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

    public function testGetCommentTextReturnstext() {
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

    public function testGetCommentTextReturnsNull() {
        $controller = new CommentsController();
        $commentText = $controller->_getCommentText(PHP_INT_MAX);

        $this->assertNull($commentText);
    }

    protected function getTestComment(): Comment {
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

    public function testFilterComments() {
        $page = $this->getTestComment();
        $controller = new CommentsController();
        
        $comments = $controller->_filterComments(
                CommentStatus::PUBLISHED, $page->getContentId());

        $this->assertCount(1, $comments);
    }

}
