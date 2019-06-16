<?php

use UliCMS\Data\Content\Comment;

require_once Path::resolve("ULICMS_ROOT/templating.php");

class PageTest extends \PHPUnit\Framework\TestCase {

    private $user;
    private $commentsInitialEnabled;
    private $initialCommentableContentTypes;
    private $ipsum = 'Lorem ipsum dolor sit amet,
		[module="fortune2"]
		[module="test"]
		[module=&quot;hello&quot;]
		consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';

    public function setUp() {
        @session_start();

        $manager = new UserManager();
        $users = $manager->getAllUsers();
        $this->user = $users[0];

        $this->commentsInitialEnabled = Settings::get("comments_enabled");
        $this->initialCommentableContentTypes = Settings::get("commentable_content_types");

        $_SERVER['HTTP_HOST'] = "company.com";
        $_SESSION["language"] = "de";

        $this->cleanUp();
    }

    public function tearDown() {
        @session_destroy();
        $this->cleanUp();

        unset($_SERVER['HTTP_HOST']);

        if ($this->commentsInitialEnabled) {
            Settings::set("comments_enabled", "1");
        } else {
            Settings::delete("comments_enabled");
        }
        if ($this->initialCommentableContentTypes) {
            Settings::set("commentable_content_types", $this->initialCommentableContentTypes);
        } else {
            Settings::delete("commentable_content_types");
        }
    }

    private function cleanUp() {
        Database::query("delete from {prefix}content where slug = 'testdisableshortcodes' or title like 'Unit Test%'", true);

        Settings::delete("comments_enabled");
        Settings::delete("commentable_content_types");

        Vars::delete("id");
    }

    public function testGetEmbeddedModulesPage() {
        $page = new Page();
        $page->content = $this->ipsum;
        $this->assertEquals(3, count($page->getEmbeddedModules()));
    }

    public function testGetEmbeddedModulesModulePage() {
        $page = new Module_Page();

        $page->content = $this->ipsum;
        $this->assertEquals(3, count($page->getEmbeddedModules()));
        $page->module = "fortune2";
        $this->assertEquals(3, count($page->getEmbeddedModules()));
        $page->module = "pfbc_sample";
        $this->assertEquals(4, count($page->getEmbeddedModules()));
    }

    public function testDisableShortcodesTrue() {
        $page = new Page();
        $page->title = 'testDisableShortcodesTrue';
        $page->slug = 'testdisableshortcodes';
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->custom_data["disable_shortcodes"] = true;
        $page->save();

        $_SESSION["language"] = 'de';
        $_GET["seite"] = "testdisableshortcodes";

        $this->assertStringNotContainsString(get_csrf_token_html(), get_content());
        $this->assertStringContainsString("[csrf_token_html]", get_content());

        $this->cleanUp();

        unset($_SESSION["language"]);
        unset($_GET["seite"]);
    }

    public function testDisableShortcodesFalse() {
        $page = new Page();
        $page->title = 'testDisableShortcodesFalse';
        $page->slug = 'testdisableshortcodes';
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->custom_data["disable_shortcodes"] = false;
        $page->save();

        $_SESSION["language"] = 'de';
        $_GET["seite"] = "testdisableshortcodes";

        $this->assertStringContainsString(get_csrf_token_html(), get_content());
        $this->assertStringNotContainsString("[csrf_token_html]", get_content());

        $this->cleanUp();

        unset($_SESSION["language"]);
        unset($_GET["seite"]);
    }

    public function testDisableShortcodesNull() {
        $page = new Page();
        $page->title = 'testDisableShortcodesNull';
        $page->slug = 'testdisableshortcodes';
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $_SESSION["language"] = 'de';
        $_GET["seite"] = "testdisableshortcodes";

        $this->assertStringContainsString(get_csrf_token_html(), get_content());
        $this->assertStringNotContainsString("[csrf_token_html]", get_content());
        $this->cleanUp();

        unset($_SESSION["language"]);
        unset($_GET["seite"]);
    }

    public function testCreatePageWithCommentsEnabledTrue() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $this->assertNotNull($page->id);

        $page = new Page($page->id);
        $this->assertTrue($page->comments_enabled);

        $this->cleanUp();
    }

    public function testCreatePageWithCommentsEnabledFalse() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = false;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $this->assertNotNull($page->id);

        $page = new Page($page->id);
        $this->assertFalse($page->comments_enabled);

        $this->cleanUp();
    }

    public function testCreatePageWithCommentsEnabledNull() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = null;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $this->assertNotNull($page->id);

        $page = new Page($page->id);
        $this->assertNull($page->comments_enabled);

        $this->cleanUp();
    }

    public function testUpdatePageWithCommentsEnabledTrue() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $this->assertNotNull($page->id);

        $page->comments_enabled = true;
        $page->save();

        $page = new Page($page->id);
        $this->assertTrue($page->comments_enabled);

        $this->cleanUp();
    }

    public function testUpdatePageWithCommentsEnabledFalse() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->author_id = $this->user->getId();
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $this->assertNotNull($page->id);

        $page->comments_enabled = false;
        $page->save();

        $page = new Page($page->id);
        $this->assertFalse($page->comments_enabled);

        $this->cleanUp();
    }

    public function testUpdatePageWithCommentsEnabledNull() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $this->assertNotNull($page->id);

        $page->comments_enabled = null;
        $page->save();

        $page = new Page($page->id);
        $this->assertNull($page->comments_enabled);

        $this->cleanUp();
    }

    public function testAreCommentsEnabledPageTrue() {
        $page = new Page();
        $page->comments_enabled = true;
        $this->assertTrue($page->areCommentsEnabled());
    }

    public function testAreCommentsEnabledPageFalse() {
        $page = new Page();
        $page->comments_enabled = false;
        $this->assertFalse($page->areCommentsEnabled());
    }

    public function testAreCommentsEnabledSettingsTrue() {
        $page = new Page();
        $page->comments_enabled = null;

        Settings::set("comments_enabled", "1");

        $this->assertTrue($page->areCommentsEnabled());
        $this->cleanUp();
    }

    public function testAreCommentsEnabledSettingsFalse() {
        $page = new Page();
        $page->comments_enabled = null;

        Settings::delete("comments_enabled");

        $this->assertFalse($page->areCommentsEnabled());
    }

    public function testHasCommentsReturnTrue() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $comment = new Comment();
        $comment->setContentId($page->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 1");
        $comment->save();

        $comment = new Comment();
        $comment->setContentId($page->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 2");
        $comment->save();

        $time = time();
        $comment->setDate($time);

        $comment->save();

        $this->assertTrue($page->hasComments());

        $this->cleanUp();
    }

    public function testHasCommentsReturnFalse() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $this->assertFalse($page->hasComments());

        $this->cleanUp();
    }

    public function testGetCommentsReturnsArrayWithResults() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $comment = new Comment();
        $comment->setContentId($page->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Kommentar 1");
        $comment->save();

        $comment = new Comment();
        $comment->setContentId($page->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Kommentar 2");
        $comment->save();

        $time = time();
        $comment->setDate($time);

        $comment->save();

        $this->assertCount(2, $page->getComments());
        $this->assertEquals("Kommentar 1", $page->getComments("date asc")[0]->getText());
        $this->assertEquals("Kommentar 2", $page->getComments("date asc")[1]->getText());

        $this->cleanUp();
    }

    public function testGetCommentsReturnsEmptyArray() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $this->assertCount(0, $page->getComments());

        $this->cleanUp();
    }

    public function testGetUrlWithSuffix() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = false;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $url = $page->getUrl("foo=bar&hello=world");
        $this->assertStringStartsWith("http", $url);
        $this->assertStringContainsString("//company.com", $url);

        $this->assertStringContainsString("{$page->slug}.html", $url);
        $this->assertStringEndsWith("foo=bar&hello=world", $url);

        $this->cleanUp();
    }

    public function testGetUrlWithoutSuffix() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = false;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $url = $page->getUrl();
        $this->assertStringStartsWith("http", $url);
        $this->assertStringContainsString("//company.com", $url);

        $this->assertStringContainsString("{$page->slug}.html", $url);

        $this->cleanUp();
    }

    public function testIncludeShortcodeShouldIncludeOtherPages() {
        $snippet = new Snippet();
        $snippet->title = 'Unit Test ' . time();
        $snippet->slug = 'unit-test-' . time();
        $snippet->language = 'de';
        $snippet->content = "even more text";
        $snippet->comments_enabled = false;
        $snippet->author_id = 1;
        $snippet->group_id = 1;
        $snippet->save();
        $shortcode = "This is [include={$snippet->id}]";

        Vars::set("id", time());

        $this->assertEquals("This is even more text", replaceShortcodesWithModules($shortcode));

        $this->cleanUp();
    }

    public function testIncludeShortcodeShouldNotIncludeItself() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = false;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $shortcode = "[include={$page->id}]";

        Vars::set("id", $page->id);

        $this->assertEquals($shortcode, replaceShortcodesWithModules($shortcode));

        $this->cleanUp();
    }

    public function testCustomDataJsonIsObjectByDefault() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = false;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();
        $page->save();

        $raw = Database::fetchFirst(Database::selectAll("content", array("custom_data"), "id = {$page->id}"));
        $this->assertEquals('{}', $raw->custom_data);
    }

}
