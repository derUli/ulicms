<?php

use UliCMS\Models\Content\Comment;
use UliCMS\Constants\CommentStatus;
use UliCMS\Exceptions\DatasetNotFoundException;
use UliCMS\Utils\CacheUtil;

class CommentTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        require_once getLanguageFilePath("en");
        require_once ModuleHelper::buildModuleRessourcePath("core_comments", "lang/en.php");
        require_once ModuleHelper::buildModuleRessourcePath("core_forms", "lang/en.php");
    }

    protected function tearDown(): void
    {
        Database::deleteFrom("comments", "text like 'Unit Test%'");
        unset($_POST["my_homepage_url"]);
        CacheUtil::clearCache();
        Database::query("update {prefix}comments set ip = null", true);
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

    public function testGetUnreadCount()
    {
        $content = ContentFactory::getAll();
        $first = $content[0];
        $second = $content[1];

        $comment = new Comment();
        $comment->setContentId($first->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 1");
        $comment->setRead(false);
        $time = time();
        $comment->setDate($time);

        $comment->save();

        $this->assertGreaterThanOrEqual(1, Comment::getUnreadCount());
    }

    public function testGetReadCount()
    {
        $content = ContentFactory::getAll();
        $first = $content[0];
        $second = $content[1];

        $comment = new Comment();
        $comment->setContentId($first->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 1");
        $comment->setRead(true);

        $time = time();
        $comment->setDate($time);

        $comment->save();

        $this->assertGreaterThanOrEqual(1, Comment::getReadCount());
    }

    public function testGetAllCount()
    {
        $content = ContentFactory::getAll();
        $first = $content[0];
        $second = $content[1];

        $comment = new Comment();
        $comment->setContentId($first->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 1");
        $comment->setRead(true);

        $time = time();
        $comment->setDate($time);

        $comment->save();
        $content = ContentFactory::getAll();
        $first = $content[0];
        $second = $content[1];

        $comment = new Comment();
        $comment->setContentId($first->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 1");
        $comment->setRead(false);

        $time = time();
        $comment->setDate($time);

        $comment->save();
        $this->assertGreaterThanOrEqual(1, Comment::getAllCount());
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
        $second = $content[1];

        $comment = new Comment();
        $comment->setContentId($first->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 1");

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
        $this->assertEquals($time, $comment->getDate());
        $this->assertEquals("Unit Test 1", $comment->getText());
        $this->assertFalse($comment->isRead());

        $comment->setContentId($second->id);
        $comment->setAuthorName("Max Muster");
        $comment->setAuthorEmail("max@muster.de");
        $comment->setAuthorUrl("http://max-muster.de.de");
        $comment->setIp("100.100.100.100");
        $comment->setUserAgent("Mozilla/5.0 (Macintosh; PPC Mac OS X x.y; rv:10.0) Gecko/20100101 Firefox/10.0");
        $comment->setText("Unit Test 2");
        $comment->setRead(true);

        $time = time() + 5;

        $comment->setDate($time);

        $comment->save();


        $comment = new Comment($id);

        $this->assertEquals($second->id, $comment->getContentID());
        $this->assertEquals("Max Muster", $comment->getAuthorName());
        $this->assertEquals("max@muster.de", $comment->getAuthorEmail());
        $this->assertEquals("http://max-muster.de.de", $comment->getAuthorUrl());
        $this->assertEquals("100.100.100.100", $comment->getIp());
        $this->assertEquals("Mozilla/5.0 (Macintosh; PPC Mac OS X x.y; rv:10.0) Gecko/20100101 Firefox/10.0", $comment->getUserAgent());
        $this->assertEquals($time, $comment->getDate());
        $this->assertEquals("Unit Test 2", $comment->getText());
        $this->assertTrue($comment->isRead());

        $comment->delete();

        try {
            $comment = new Comment($id);
            $this->fail("expected exception not thrown");
        } catch (DatasetNotFoundException $e) {
            $this->assertEquals("no comment with id " . intval($id), $e->getMessage());
        }
    }

    public function testGetContentWithContentId()
    {
        $contents = ContentFactory::getAll();
        $first = $contents[0];

        $comment = new Comment();
        $comment->setContentId($first->id);

        $content = $comment->getContent();
        $this->assertTrue(is_subclass_of($content, Content::class));
        $this->assertEquals($first->id, $content->getId());
    }

    public function testGetContentWithNull()
    {
        $comment = new Comment();
        $this->assertNull($comment->getContent());
    }

    public function testGetAll()
    {
        $contents = ContentFactory::getAll();
        $first = $contents[0];

        $comment = new Comment();
        $comment->setContentId($first->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 2");

        $time = time();
        $comment->setDate($time);

        $comment->save();

        $comment = new Comment();
        $comment->setContentId($first->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 3");

        $comment->save();

        $time = time();
        $comment->setDate($time);

        $comment->save();

        $comments = Comment::getAll();
        $this->assertGreaterThanOrEqual(2, count($comments));

        foreach ($comments as $comment) {
            $this->assertInstanceOf(Comment::class, $comment);
            $this->assertNotNull($comment->getId());
            $this->assertNotNull($comment->getContentId());
        }
    }

    public function testGetAllByContentId()
    {
        $contents = ContentFactory::getAll();
        $last = array_pop($contents);

        $comment = new Comment();
        $comment->setContentId($last->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 4");

        $time = time();
        $comment->setDate($time);

        $comment->save();

        $comment = new Comment();
        $comment->setContentId($last->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 5");

        $time = time();
        $comment->setDate($time);

        $comment->save();

        $comment = new Comment();

        $comment->setContentId($last->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 6");

        $comment->save();

        $time = time();
        $comment->setDate($time);

        $comment->save();

        $comments = Comment::getAllByContentId($last->id, "id desc");

        $this->assertGreaterThanOrEqual(3, count($comments));

        $comment = array_shift($comments);

        $this->assertNotNull($comment->getID());
        $this->assertEquals("Unit Test 6", $comment->getText());

        $comment = array_shift($comments);
        $this->assertNotNull($comment->getID());
        $this->assertEquals("Unit Test 5", $comment->getText());

        $comment = array_shift($comments);
        $this->assertNotNull($comment->getID());
        $this->assertEquals("Unit Test 4", $comment->getText());
    }

    public function testGetAllcoByStatus()
    {
        $contents = ContentFactory::getAll();
        $last = array_pop($contents);
        $comment = new Comment();
        $comment->setContentId($last->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setStatus(CommentStatus::SPAM);
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 7");

        $time = time();
        $comment->setDate($time);

        $comment->save();

        $comment = new Comment();
        $comment->setContentId($last->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setStatus(CommentStatus::SPAM);
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 8");

        $comment->save();

        $comment = new Comment();
        $comment->setContentId($last->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setStatus(CommentStatus::PUBLISHED);
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 9");

        $comment->save();

        $time = time();
        $comment->setDate($time);

        $comment->save();

        $comments = Comment::getAllByStatus(CommentStatus::SPAM, $last->id);

        $this->assertCount(2, $comments);

        foreach ($comments as $comment) {
            $this->assertEquals(CommentStatus::SPAM, $comment->getStatus());
        }
        $comments = Comment::getAllByStatus(CommentStatus::PUBLISHED, $last->id);
        $this->assertCount(1, $comments);

        foreach ($comments as $comment) {
            $this->assertEquals(CommentStatus::PUBLISHED, $comment->getStatus());
        }
    }

    public function testSetUrlWithFullUrl()
    {
        $comment = new Comment();
        $comment->setAuthorUrl("https://www.ulicms.de");
        $this->assertEquals("https://www.ulicms.de", $comment->getAuthorUrl());
    }

    public function testSetUrlWithNoUrl()
    {
        $comment = new Comment();
        $comment->setAuthorUrl("this is not an url");
        $this->assertNull($comment->getAuthorUrl());
    }

    public function testSetUrlWithIncompleteHttpUrl()
    {
        $comment = new Comment();
        $comment->setAuthorUrl("http://");
        $this->assertNull($comment->getAuthorUrl());
    }

    public function testSetUrlWithIncompleteHttpsUrl()
    {
        $comment = new Comment();
        $comment->setAuthorUrl("https://");
        $this->assertNull($comment->getAuthorUrl());
    }

    public function testSetUrlWithIncompleteFtpUrl()
    {
        $comment = new Comment();
        $comment->setAuthorUrl("ftp://");
        $this->assertNull($comment->getAuthorUrl());
    }

    public function testCheckIfCommentWithIpExistsTrue()
    {
        $content = ContentFactory::getAll();
        $first = $content[0];

        $comment = new Comment();
        $comment->setContentId($first->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("222.222.222.222");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 1");
        $comment->setStatus(CommentStatus::SPAM);
        $comment->save();

        $this->assertTrue(Comment::checkIfCommentWithIpExists("222.222.222.222", CommentStatus::SPAM));

        $comment->setStatus(CommentStatus::PUBLISHED);
        $comment->save();

        $this->assertFalse(Comment::checkIfCommentWithIpExists("222.222.222.222", CommentStatus::SPAM));

        $this->assertTrue(Comment::checkIfCommentWithIpExists("222.222.222.222", CommentStatus::PUBLISHED));

        $comment->delete();
    }

    public function testCheckIfCommentWithIpExistsFalse()
    {
        $this->assertFalse(Comment::checkIfCommentWithIpExists("111.111.111.111", CommentStatus::SPAM));
        $this->assertFalse(Comment::checkIfCommentWithIpExists("111.111.111.111", CommentStatus::PUBLISHED));
        $this->assertFalse(Comment::checkIfCommentWithIpExists("111.111.111.111", CommentStatus::PENDING));
    }

    public function testDataWithInvalidValueThrowsException()
    {
        $this->expectException("InvalidArgumentException");
        $comment = new Comment();
        $comment->setDate(new UliCMSVersion());
    }

    public function testDeleteIps()
    {
        $this->createComments();
        $this->assertEquals(6, Comment::deleteIpsAfter48Hours());
        $this->assertEquals(3, $this->countIpsInDatabase());
    }

    public function testDeleteIpsKeepSpamIps()
    {
        $this->createComments();
        $this->assertEquals(3, Comment::deleteIpsAfter48Hours(true));
        $this->assertEquals(6, $this->countIpsInDatabase());
    }

    protected function createComments()
    {
        $content = ContentFactory::getAll();
        $first = $content[0];

        $this->createComment(
            $first->id,
            CommentStatus::SPAM,
            1587888906
        );

        $this->createComment(
            $first->id,
            CommentStatus::PUBLISHED,
            1587888906
        );

        $this->createComment(
            $first->id,
            CommentStatus::PUBLISHED,
            time() - 100
        );
    }

    protected function createComment($contentId, $status, $timestamp)
    {
        for ($i = 1; $i <= 3; $i++) {
            $comment = new Comment();
            $comment->setContentId($contentId);
            $comment->setAuthorName("John Doe");
            $comment->setAuthorEmail("john@doe.de");
            $comment->setAuthorUrl("http://john-doe.de");
            $comment->setIp("123.123.123.123");
            $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
            $comment->setText("Unit Test 1");
            $comment->setStatus($status);
            $comment->setDate($timestamp);
            $comment->save();
        }
    }

    protected function countIpsInDatabase(): int
    {
        $query = Database::selectAll("comments", ["id"], "ip is not null");
        return Database::getNumROws($query);
    }
}
