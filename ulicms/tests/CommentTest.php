<?php
use UliCMS\Data\Content\Comment;
use UliCMS\Exceptions\FileNotFoundException;

class CommentTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        include_once getLanguageFilePath("en");
        include_once ModuleHelper::buildModuleRessourcePath("core_comments", "lang/en.php");
        include_once ModuleHelper::buildModuleRessourcePath("core_forms", "lang/en.php");
    }

    public function tearDown()
    {
        Database::deleteFrom("comments", "content like 'Unit Test%'");
        unset($_POST["my_homepage_url"]);
    }

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

    public function testIsSpamNull()
    {
        $comment = new Comment();
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        
        $comment->setIp("123.123.123.123");
        
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setDate(time());
        $this->assertNull($comment->isSpam());
    }

    public function testIsSpamError()
    {
        $_POST["my_homepage_url"] = "http://www.ulicms.de";
        
        $comment = new Comment();
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        
        $comment->setIp("123.123.123.123");
        
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setDate(time());
        
        $result = $comment->isSpam();
        
        $this->assertNotNull($result);
        $this->assertCount(1, $result);
        
        $error = $result[0];
        
        $this->assertEquals('Honeypot', $error->field);
        $this->assertEquals('Honeypot is not empty!', $error->message);
        
        unset($_POST["my_homepage_url"]);
    }

    public function testCreateUpdateAndDelete()
    {
        $content = ContentFactory::getAll();
        $first = $content[0];
        
        $comment = new Comment();
        $comment->setContentId($first->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setContent("Unit Test 1");
        
        $time = time();
        $comment->setDate($time);
        
        $comment->save();
        
        $this->assertNotNull($comment->getID());
        
        $id = $comment->getID();
        
        $comment = new Comment($id);
        $this->assertEquals($first->id, $comment->getContentID());
        $this->assertEquals("John Doe", $comment->getAuthorName());
        $this->assertEquals("john@doe.de", $comment->getAuthorEmail());
        $this->assertEquals("http://john-doe.de", $comment->getAuthorUrl());
        $this->assertEquals("123.123.123.123", $comment->getIp());
        $this->assertEquals("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36", $comment->getUserAgent());
        $this->assertEquals(time(), $comment->getDate());
        $this->assertEquals("Unit Test 1", $comment->getContent());
        
        // TODO: Test fürs Update implementieren
        
        $comment->delete();
        
        try {
            $comment = new Comment($id);
            $this->fail("expected exception not thrown");
        } catch (FileNotFoundException $e) {
            $this->assertEquals("no comment with id " . intval($id), $e->getMessage());
        }
    }
}
