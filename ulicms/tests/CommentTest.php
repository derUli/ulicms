<?php
use UliCMS\Data\Content\Comment;

class CommentTest extends \PHPUnit\Framework\TestCase
{

    public function testSetContentIdInvalidArgument()
    {
        $comment = new Comment();
        try {
            $comment->setContentId("foo");
            $this->fail("expected exception not thrown");
        } catch (InvalidArgumentException $e) {
            $this->assertEquals("foo is not a number", $e->getMessage());
        }
    }

    public function testSetAuthorNameInvalidArgument()
    {
        $comment = new Comment();
        try {
            $comment->setAuthorName(123);
            $this->fail("expected exception not thrown");
        } catch (InvalidArgumentException $e) {
            $this->assertEquals("123 is not a string", $e->getMessage());
        }
    }

    public function tesSetDateInvalidArgument()
    {
        $comment = new Comment();
        try {
            $comment->setDate("foo");
            $this->fail("expected exception not thrown");
        } catch (InvalidArgumentException $e) {
            $this->assertEquals("foo is not an integer timestamp", $e->getMessage());
        }
    }

    public function testSetStatusInvalidArgument()
    {
        $comment = new Comment();
        try {
            $comment->setStatus(123);
            $this->fail("expected exception not thrown");
        } catch (InvalidArgumentException $e) {
            $this->assertEquals("123 is not a status string", $e->getMessage());
        }
    }
}
