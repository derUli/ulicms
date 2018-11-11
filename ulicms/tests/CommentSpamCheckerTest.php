<?php
use UliCMS\Security\SpamChecker\SpamFilterConfiguration;
use UliCMS\Data\Content\Comment;
use UliCMS\Security\SpamChecker\CommentSpamChecker;

class CommentSpamCheckerTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        include_once getLanguageFilePath("en");
        include_once ModuleHelper::buildModuleRessourcePath("core_comments", "lang/en.php");
        include_once ModuleHelper::buildModuleRessourcePath("core_forms", "lang/en.php");
    }

    public function testConstructor()
    {
        $configuration = new SpamFilterConfiguration();
        $comment = new Comment();
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertCount(0, $checker->getErrors());
    }

    public function testSpamWithBadwords()
    {
        $configuration = new SpamFilterConfiguration();
        $configuration->setBadwords(array(
            "Shit",
            "Fuck",
            "Cock",
            "Viagra"
        ));
        $comment = new Comment();
        $comment->setAuthorName("Motherfucker");
        $comment->setContent("hey, you motherfucker! You are a shit cock! You need cheap viagra!");
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertTrue($checker->doSpamCheck());
        
        $errors = $checker->getErrors();
        $this->assertCount(2, $errors);
        
        $error0 = $errors[0];
        $error1 = $errors[1];
        
        $this->assertEquals('Author Name', $error0->field);
        $this->assertEquals('Comment Text', $error1->field);
        
        $this->assertEquals('The field "Author Name" contains the not allowed word "Fuck"', $error0->message);
        $this->assertEquals('The field "Comment Text" contains the not allowed word "Shit"', $error1->message);
    }

    public function testSpamWithInvalidMxEntry()
    {
        $configuration = new SpamFilterConfiguration();
        $configuration->setCheckMxOfMailAddress(true);
        $comment = new Comment();
        $comment->setAuthorName("Motherfucker");
        $comment->setContent("hey, you motherfucker! You are a shit cock! You need cheap viagra!");
        $comment->setAuthorEmail("shittyspammer@example.org");
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertTrue($checker->doSpamCheck());
        $errors = $checker->getErrors();
        $this->assertCount(1, $errors);
        $error = $errors[0];
        
        $this->assertEquals('Author E-Mail Address', $error->field);
        $this->assertEquals('The domain of your e-Mail address has no valid MX entry. Please verify that your e-Mail address in valid.', $error->message);
    }
}