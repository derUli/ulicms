<?php
use UliCMS\Security\SpamChecker\SpamFilterConfiguration;
use UliCMS\Data\Content\Comment;
use UliCMS\Security\SpamChecker\CommentSpamChecker;

class CommentSpamCheckerTest extends \PHPUnit\Framework\TestCase
{

    public function testConstructor()
    {
        $configuration = new SpamFilterConfiguration();
        $comment = new Comment();
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertCount(0, $checker->getErrors());
    }
}
	