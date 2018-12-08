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

    public function tearDown()
    {
        unset($_POST["my_homepage_url"]);
    }

    public function testConstructor()
    {
        $configuration = new SpamFilterConfiguration();
        $comment = new Comment();
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertCount(0, $checker->getErrors());
    }

    public function testSpamWithNonEmptyHoneypotField()
    {
        $configuration = new SpamFilterConfiguration();
        
        $comment = new Comment();
        $comment->setAuthorName("Motherfucker");
        $comment->setText("hey, you motherfucker! You are a shit cock! You need cheap viagra!");
        
        $_POST["my_homepage_url"] = "http://www.google.de";
        
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertTrue($checker->doSpamCheck());
        
        $errors = $checker->getErrors();
        $this->assertCount(1, $errors);
        
        $error = $errors[0];
        
        $this->assertEquals('Honeypot', $error->field);
        $this->assertEquals('Honeypot is not empty!', $error->message);
        
        unset($_POST["my_homepage_url"]);
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
        $comment->setText("hey, you motherfucker! You are a shit cock! You need cheap viagra!");
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertTrue($checker->doSpamCheck());
        
        $errors = $checker->getErrors();
        $this->assertCount(2, $errors);
        
        $error0 = $errors[0];
        $error1 = $errors[1];
        
        $this->assertEquals('Author Name', $error0->field);
        $this->assertEquals('Comment Text', $error1->field);
        
        $this->assertEquals('The field "Author Name" contains the not allowed word "Fuck".', $error0->message);
        $this->assertEquals('The field "Comment Text" contains the not allowed word "Shit".', $error1->message);
    }

    public function testSpamWithInvalidMxEntry()
    {
        $configuration = new SpamFilterConfiguration();
        $configuration->setCheckMxOfMailAddress(true);
        
        $comment = new Comment();
        $comment->setAuthorName("Motherfucker");
        $comment->setText("hey, you motherfucker! You are a shit cock! You need cheap viagra!");
        $comment->setAuthorEmail("shittyspammer@example.org");
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertTrue($checker->doSpamCheck());
        $errors = $checker->getErrors();
        $this->assertCount(1, $errors);
        $error = $errors[0];
        
        $this->assertEquals('Author E-Mail Address', $error->field);
        $this->assertEquals('The domain of your e-Mail address has no valid MX entry. Please verify that your e-Mail address in valid.', $error->message);
    }

    public function testWithBotUseragent()
    {
        $configuration = new SpamFilterConfiguration();
        $configuration->setRejectRequestsFromBots(true);
        
        $comment = new Comment();
        $comment->setAuthorName("Motherfucker");
        $comment->setText("hey, you motherfucker! You are a shit cock! You need cheap viagra!");
        $comment->setAuthorEmail("shittyspammer@example.org");
        $comment->setUserAgent("libwww-perl/5.805");
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertTrue($checker->doSpamCheck());
        $errors = $checker->getErrors();
        $this->assertCount(1, $errors);
        $error = $errors[0];
        
        $this->assertEquals('User Agent', $error->field);
        $this->assertEquals('You look like a bot. Bots are not allowed to send messages on this website.', $error->message);
    }

    public function testSpamWithChineseChars()
    {
        $configuration = new SpamFilterConfiguration();
        $configuration->setDisallowChineseChars(true);
        
        $comment = new Comment();
        $comment->setAuthorName("中國人在科騰 vong China");
        $comment->setText("Yo! 中国人在科腾 Yo!");
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertTrue($checker->doSpamCheck());
        
        $errors = $checker->getErrors();
        $this->assertCount(2, $errors);
        
        $error0 = $errors[0];
        $error1 = $errors[1];
        
        $this->assertEquals('Author Name', $error0->field);
        $this->assertEquals('Comment Text', $error1->field);
        
        $this->assertEquals("Chinese chars are not allowed!", $error0->message);
        $this->assertEquals("Chinese chars are not allowed!", $error1->message);
    }

    public function testSpamWithCyrillicChars()
    {
        $configuration = new SpamFilterConfiguration();
        $configuration->setDisallowCyrillicChars(true);
        
        $comment = new Comment();
        $comment->setAuthorName("Гомофобный русский диктатор Путин");
        $comment->setText("Yo!Православная Церковь - это Yo!");
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertTrue($checker->doSpamCheck());
        
        $errors = $checker->getErrors();
        $this->assertCount(2, $errors);
        
        $error0 = $errors[0];
        $error1 = $errors[1];
        
        $this->assertEquals('Author Name', $error0->field);
        $this->assertEquals('Comment Text', $error1->field);
        
        $this->assertEquals("Cyrillic chars are not allowed!", $error0->message);
        $this->assertEquals("Cyrillic chars are not allowed!", $error1->message);
    }

    public function testWithBlockedCountries()
    {
        $configuration = new SpamFilterConfiguration();
        
        $configuration->setBlockedCountries(array(
            "vn",
            "cn",
            "ru"
        ));
        
        $comment = new Comment();
        $comment->setAuthorName("Гомофобный русский диктатор Путин");
        $comment->setText("Yo!Православная Церковь - это Yo!");
        $comment->setIp("123.30.54.106");
        
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertTrue($checker->doSpamCheck());
        
        $errors = $checker->getErrors();
        $this->assertCount(1, $errors);
        
        $error = $errors[0];
        
        $this->assertEquals('IP Address', $error->field);
        $this->assertEquals("Access to this function for your country is blocked!\nYour hostname: static.vnpt.vn", $error->message);
    }

    public function testSpamfilterDisabled()
    {
        $configuration = new SpamFilterConfiguration();
        $configuration->setSpamFilterEnabled(false);
        
        $comment = new Comment();
        $comment->setAuthorName("Motherfucker");
        $comment->setText("hey, you motherfucker! You are a shit cock! You need cheap viagra!");
        
        $_POST["my_homepage_url"] = "http://www.google.de";
        
        $checker = new CommentSpamChecker($comment, $configuration);
        $this->assertFalse($checker->doSpamCheck());
        
        $errors = $checker->getErrors();
        $this->assertCount(0, $errors);
        
        unset($_POST["my_homepage_url"]);
    }

    public function testNoSpamWithAllOptions()
    {
        $configuration = new SpamFilterConfiguration();
        $configuration->setSpamFilterEnabled(true);
        $configuration->setBadwords("shit", "fuck", "crap");
        $configuration->setBlockedCountries("vn", "cn", "ir");
        $configuration->setCheckMxOfMailAddress(true);
        $configuration->setDisallowChineseChars(true);
        $configuration->setDisallowCyrillicChars(true);
        $configuration->setRejectRequestsFromBots(true);
        
        $comment = new Comment();
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setDate(time());
        
        $checker = new CommentSpamChecker($comment, $configuration);
        
        $this->assertFalse($checker->doSpamCheck());
        
        $this->assertCount(0, $checker->getErrors());
    }
}