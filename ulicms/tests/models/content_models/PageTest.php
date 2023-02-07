<?php

use App\Models\Content\Comment;
use App\Models\Content\VCS;
use App\Exceptions\DatasetNotFoundException;
use UliCMS\Security\Permissions\PagePermissions;

class PageTest extends \PHPUnit\Framework\TestCase {

    private $user;
    private $commentsInitialEnabled;
    private $initialCommentablfeContentTypes;
    private $ipsum = 'Lorem ipsum dolor sit amet,
		[module="fortune2"]
		[module="test"]
		[module=&quot;hello&quot;]
		consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';
    private $savedSettings = [];

    protected function setUp(): void {
        $manager = new UserManager();
        $users = $manager->getAllUsers();
        $this->user = $users[0];

        $this->commentsInitialEnabled = Settings::get("comments_enabled");
        $this->initialCommentableContentTypes = Settings::get("commentable_content_types");

        $_SERVER['HTTP_HOST'] = "company.com";
        $_SESSION["language"] = "de";
        $_SERVER["REQUEST_URI"] = "/";

        $settings = array(
            "frontpage",
            "frontpage_de",
            "frontpage_en"
        );

        foreach ($settings as $setting) {
            $this->savedSettings[$setting] = Settings::get($setting);
        }
    }

    protected function tearDown(): void {
        $_SERVER = [];
        $_GET = [];
        $_POST = [];
        $_REQUEST = [];

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
        foreach ($this->savedSettings as $key => $value) {
            Settings::set($key, $value);
        }
        Database::query("delete from {prefix}content where slug = 'testdisableshortcodes' or title like 'Unit Test%' or slug like"
                . "'unit-test%' or slug = 'testDisableShortcodesFalse'", true);

        Settings::delete("comments_enabled");
        Settings::delete("commentable_content_types");

        Vars::clear();
    }

    public function testGetEmbeddedModulesPage() {
        $page = new Page();
        $page->content = $this->ipsum;
        $this->assertEquals(3, count($page->getEmbeddedModules()));
    }

    public function testContainsModuleReturnsTrue() {
        $page = new Page();
        $page->content = $this->ipsum;

        $this->assertTrue($page->containsModule());
        $this->assertTrue($page->containsModule("fortune2"));
        $this->assertTrue($page->containsModule("hello"));
    }

    public function testContainsModuleReturnsFalse() {
        $page = new Page();
        $page->content = "Hallo Welt";

        $this->assertFalse($page->containsModule());
        $this->assertFalse($page->containsModule("fortune2"));
        $this->assertFalse($page->containsModule("hello"));
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
        $page->update();

        $_SESSION["language"] = 'de';
        $_GET["slug"] = "testdisableshortcodes";

        $this->assertStringNotContainsString(get_csrf_token_html(), get_content());
        $this->assertStringContainsString("[csrf_token_html]", get_content());
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
        $page->menu_image = "foo.jpg";
        $page->custom_data = null;
        $page->save();
        $page->custom_data = null;
        $page->save();

        $_SESSION["language"] = 'de';
        $_GET["slug"] = "testdisableshortcodes";

        $this->assertStringContainsString(get_csrf_token_html(), get_content());
        $this->assertStringNotContainsString("[csrf_token_html]", get_content());
    }

    public function testGetShowHeadlineReturnsTrue() {
        $page = new Page();

        $this->assertTrue($page->getShowHeadline());
        $page->title = 'testDisableShortcodesNull';
        $page->slug = 'testdisableshortcodes';
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;

        $page->save();

        $savedPage = ContentFactory::getById($page->id);
        $this->assertTrue($savedPage->getShowHeadline());
    }

    public function testGetShowHeadlineReturnsFalse() {
        $page = new Page();

        $page->title = 'testDisableShortcodesNull';
        $page->slug = 'testdisableshortcodes';
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->show_headline = false;

        $page->save();

        $savedPage = ContentFactory::getById($page->id);
        $this->assertFalse($savedPage->getShowHeadline());
    }

    public function testGetHeadlineReturnsTitle() {
        $page = new Page();
        $page->title = 'Originaler Titel';
        $page->slug = 'testdisableshortcodes';
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $savedPage = ContentFactory::getById($page->id);
        $this->assertEquals('Originaler Titel', $savedPage->getHeadline());
    }

    public function testGetHeadlineReturnsAlternateTitle() {
        $page = new Page();

        $page->title = 'Originaler Titel';
        $page->alternate_title = "Alternativer Titel";
        $page->slug = 'testdisableshortcodes';
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;

        $page->save();

        $savedPage = ContentFactory::getById($page->id);
        $this->assertEquals('Alternativer Titel', $savedPage->getHeadline());
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
        $_GET["slug"] = "testdisableshortcodes";

        $this->assertStringContainsString(get_csrf_token_html(), get_content());
        $this->assertStringNotContainsString("[csrf_token_html]", get_content());
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

    public function testAreCommentsEnabledWithTypesReturnsTrue() {
        Settings::set("comments_enabled", "1");
        Settings::set("commentable_content_types", "page;article");

        $page = new Page();
        $this->assertTrue($page->areCommentsEnabled());
    }

    public function testAreCommentsEnabledWithTypesReturnFalse() {
        Settings::set("comments_enabled", "1");
        Settings::set("commentable_content_types", "page;article");

        $page = new Image_Page();
        $this->assertFalse($page->areCommentsEnabled());
    }

    public function testAreCommentsEnabledSettingsTrue() {
        $page = new Page();
        $page->comments_enabled = null;

        Settings::set("comments_enabled", "1");

        $this->assertTrue($page->areCommentsEnabled());
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
    }

    public function testIsDeletedReturnsFalse() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();
        $this->assertFalse($page->isDeleted());
    }

    public function testIsDeletedReturnsTrue() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();
        $page->delete();

        $this->assertTrue($page->isDeleted());
    }

    public function testCreateDeleted() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->delete();

        $this->assertTrue($page->isDeleted());
    }

    public function testGetDeletedAtReturnsNull() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();
        $this->assertNull($page->getDeletedAt());
    }

    public function testGetDeletedAtReturnsTimestamp() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();
        $page->delete();
        $this->assertGreaterThan(time() - 100, $page->getDeletedAt());
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

        $this->assertStringContainsString("{$page->slug}", $url);
        $this->assertStringEndsWith("foo=bar&hello=world", $url);
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

        $this->assertStringContainsString("{$page->slug}", $url);
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
    }

    public function testCreatePageWithMetaDescriptionNull() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->meta_description = null;
        $page->meta_keywords = null;
        $page->save();

        $this->assertNotNull($page->id);

        $page = new Page($page->id);
        $this->assertNull($page->meta_description);
        $this->assertNull($page->meta_keywords);
        $this->assertNull($page->robots);
    }

    public function testUpdatePageWithMetaDescriptionNull() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->meta_description = 'foo';
        $page->meta_keywords = 'foo';

        $page->save();
        $this->assertEquals("foo", $page->meta_description);
        $this->assertEquals("foo", $page->meta_keywords);

        $page->meta_description = null;
        $page->meta_keywords = null;
        $page->save();

        $this->assertNotNull($page->id);

        $page = new Page($page->id);
        $this->assertNull($page->meta_description);
        $this->assertNull($page->meta_keywords);
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
        $page->custom_data = new stdClass();
        $page->save();

        $raw = Database::fetchFirst(Database::selectAll("content", array("custom_data"), "id = {$page->id}"));
        $this->assertEquals('{}', $raw->custom_data);
    }

    public function testHasChildrenReturnsTrue() {
        $result = Database::pQuery("select parent_id from {prefix}content where "
                        . "parent_id is not null", [], true);
        $dataset = Database::fetchObject($result);

        $page = ContentFactory::getByID($dataset->parent_id);
        $this->assertTrue($page->hasChildren());
    }

    public function testHasChildrenReturnsFalse() {
        $page = new Page();

        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;
        $page->save();

        $this->assertFalse($page->hasChildren());
    }

    public function testGetChildrenReturnsTrue() {
        $result = Database::pQuery("select parent_id from {prefix}content where "
                        . "parent_id is not null", [], true);
        $dataset = Database::fetchObject($result);

        $page = ContentFactory::getByID($dataset->parent_id);
        $children = $page->getChildren();
        $this->assertGreaterThanOrEqual(1, count($children));

        foreach ($children as $child) {
            $this->assertEquals($page->id, $child->parent_id);
        }
    }

    public function testGetChildrenReturnsFalse() {
        $page = new Page();

        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;
        $page->save();

        $children = $page->getChildren();
        $this->assertCount(0, $children);
    }

    public function testGetParentReturnsNull() {
        $result = Database::pQuery("select id from {prefix}content where "
                        . "parent_id is null", [], true);
        $dataset = Database::fetchObject($result);

        $page = ContentFactory::getByID($dataset->id);
        $this->assertNull($page->getParent());
    }

    public function testGetParentReturnsModel() {
        $result = Database::pQuery("select parent_id, id from {prefix}content where "
                        . "parent_id is not null", [], true);
        $dataset = Database::fetchObject($result);

        $page = ContentFactory::getByID($dataset->id);
        $this->assertInstanceOf(Content::class, $page->getParent());
        $this->assertEquals($page->getParent()->getId(), $dataset->parent_id);
        $this->assertGreaterThanOrEqual(1, count($page->getParent()->getChildren()));
    }

    public function testGetHistoryNotPersistentReturnsNothing() {
        $page = new Page();
        $this->assertCount(0, $page->getHistory());
    }

    public function testGetHistoryWithPersistentReturnsNothing() {
        $page = new Page();

        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;
        $page->save();

        $this->assertIsArray($page->getHistory());
        $this->assertCount(0, $page->getHistory());
    }

    public function testGetHistoryReturnsChanges() {
        $page = new Page();

        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;
        $page->save();

        VCS::createRevision($page->getID(), "New Text 1", 1);
        VCS::createRevision($page->getID(), "New Text 2", 1);
        VCS::createRevision($page->getID(), "New Text 3", 1);

        $this->assertIsArray($page->getHistory());
        $this->assertCount(3, $page->getHistory());
    }

    public function testIsFrontPageReturnsTrue() {
        $page = new Page();
        $page->title = 'hallo';
        $page->slug = 'unit-test-is-frontpage' . uniqid();
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;
        $page->save();

        $page->makeFrontPage();
        $this->assertTrue($page->isFrontPage());
    }

    public function testIsFrontPageReturnsFalse() {
        $page = new Page();
        $page->title = 'hallo';
        $page->slug = 'unit-test-not-frontpage' . uniqid();
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;
        $page->save();

        $this->assertFalse($page->isFrontPage());
    }

    public function testIsErrorPage403ReturnsFalse() {
        $page = new Page();
        $page->title = 'Unit Test Error Page 403';
        $page->slug = 'unit-test-error-page-403-' . uniqid();
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;
        $page->save();

        $this->assertFalse($page->isErrorPage403());
        $this->assertFalse($page->isErrorPage());
    }

    public function testIsErrorPage404ReturnsFalse() {
        $page = new Page();
        $page->title = 'Unit Test Error Page 404';
        $page->slug = 'unit-test-error-page-404-' . uniqid();
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;
        $page->save();

        $this->assertFalse($page->isErrorPage404());
        $this->assertFalse($page->isErrorPage());
    }

    public function testIsErrorPage403ReturnsTrue() {
        $page = new Page();
        $page->title = 'Unit Test Error Page 403';
        $page->slug = 'unit-test-error-page-403-' . uniqid();
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;
        $page->save();

        $page->makeErrorPage403();

        $this->assertTrue($page->isErrorPage403());
        $this->assertTrue($page->isErrorPage());

        $page->makeErrorPage403(false);
    }

    public function testIsErrorPage404ReturnsTrue() {
        $page = new Page();
        $page->title = 'Unit Test Error Page 404';
        $page->slug = 'unit-test-error-page-404-' . uniqid();
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;
        $page->save();

        $page->makeErrorPage404();

        $this->assertTrue($page->isErrorPage404());
        $this->assertTrue($page->isErrorPage());

        $page->makeErrorPage404(false);
    }

    public function testCreateWithApproved0() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->approved = 0;
        $page->meta_description = null;
        $page->meta_keywords = null;
        $page->robots = "noindex, nofollow";
        $page->save();

        $this->assertNotNull($page->id);

        $page = new Page($page->id);
        $this->assertEquals(0, $page->approved);

        $page->approved = 1;
        $page->save();

        $page = new Page($page->id);
        $this->assertEquals(1, $page->approved);
    }

    public function testCreateWithoutApproved() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->meta_description = null;
        $page->meta_keywords = null;
        $page->robots = "noindex, nofollow";
        $page->save();

        $this->assertNotNull($page->id);

        $page = new Page($page->id);
        $this->assertEquals(1, $page->approved);
    }

    public function testCreatePageWithRobots() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->meta_description = null;
        $page->meta_keywords = null;
        $page->robots = "noindex, nofollow";
        $page->save();

        $this->assertNotNull($page->id);

        $page = new Page($page->id);
        $this->assertEquals("noindex, nofollow", $page->robots);
    }

    public function testLoadByIdThrowsException() {
        $page = new Page();

        $this->expectException(DatasetNotFoundException::class);

        $page->loadByID(PHP_INT_MAX);
    }

    public function testLoadBySlugAndLanguageThrowsException() {
        $page = new Page();

        $this->expectException(DatasetNotFoundException::class);

        $page->loadBySlugAndLanguage("erdogan-kokuyor", "tr");
    }

    public function testSetAndGetPermissions() {
        $permissions = new PagePermissions(
                [
            "group" => true,
            "owner" => true
                ]
        );

        $page = new Page();
        $page->setPermissions($permissions);

        $this->assertEquals($permissions, $page->getPermissions());
    }

    public function testUndelete() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();
        $page->delete();

        $page->undelete();

        $this->assertFalse($page->isDeleted());
    }

    public function testLoadByRequestId() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->approved = 0;
        $page->save();
        $page->delete();

        $oldId = $page->getId();

        $_GET["id"] = $oldId;
        $loadedPage = new Page();
        $loadedPage->loadByRequestId();

        $this->assertEquals($oldId, $loadedPage->getId());
        $this->assertEquals("Some Text", $loadedPage->content);
    }

}
