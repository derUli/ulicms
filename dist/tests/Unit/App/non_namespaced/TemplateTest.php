<?php

use App\Exceptions\FileNotFoundException;
use App\Helpers\TestHelper;
use App\Translations\Translation;
use App\Utils\File;

use function App\HTML\stringContainsHtml;

class TemplateTest extends \PHPUnit\Framework\TestCase {
    private $savedSettings = [];

    protected function setUp(): void {
        Translation::loadAllModuleLanguageFiles('en');
        \App\Storages\Vars::setNoCache(true);

        $settings = [
            'site_slogan',
            'site_slogan_de',
            'site_slogan_en',
            'site_slogan_fr',
            'homepage_owner',
            'footer_text',
            'logo_disabled',
            'domain_to_language'
        ];
        foreach ($settings as $setting) {
            $this->savedSettings[$setting] = Settings::get($setting);
        }
        $this->setSiteSlogan();

        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/other-url.html?param=value';
        $_SESSION = [];
        $_GET = [];

        require_once getLanguageFilePath('en');
    }

    protected function tearDown(): void {
        \App\Storages\Vars::setNoCache(false);

        foreach ($this->savedSettings as $key => $value) {
            Settings::set($key, $value);
        }

        Database::query(
            'delete from {prefix}content where '
            . "title like 'Test Page %' or slug like 'testpage%' or slug"
            . " like 'test-page%' or slug ='testgetbodyclasses'",
            true
        );
        \App\Storages\Vars::delete('headline');
        \App\Storages\Vars::delete('title');

        Settings::delete('disable_no_format_detection');
        \App\Storages\Vars::delete('id');
    }

    public function testRenderPartialSuccess(): void {
        $this->assertEquals('Hello World!', Template::renderPartial('hello'));
    }

    public function testRenderPartialSuccessWithTheme(): void {
        $this->assertEquals('Hello World!', Template::renderPartial(
            'hello',
            'impro17'
        ));
    }

    public function testRenderPartialNotFound(): void {
        try {
            $nothing = Template::renderPartial('nothing', 'impro17');
            $this->fail('FileNotFoundException not thrown');
        } catch (FileNotFoundException $e) {
            $this->assertNotNull('Partial not found test successful');
        }
    }

    public function testGetHtml5Doctype(): void {
        $this->assertEquals('<!doctype html>', Template::getHtml5Doctype());
    }

    public function testHtml5Doctype(): void {
        ob_start();
        Template::html5Doctype();
        $this->assertEquals('<!doctype html>', ob_get_clean());
    }

    public function testGetYear(): void {
        $this->assertEquals(date('Y'), Template::getYear());
        $this->assertEquals(date('Y'), Template::getYear('Y'));
        $this->assertEquals(date('y'), Template::getYear('y'));
    }

    public function testYear(): void {
        ob_start();
        Template::year();
        $this->assertEquals(date('Y'), ob_get_clean());

        ob_start();
        Template::year('Y');
        $this->assertEquals(date('Y'), ob_get_clean());

        ob_start();
        Template::year('y');
        $this->assertEquals(date('y'), ob_get_clean());
    }

    public function testGetOgHTMLPrefix(): void {
        $_SESSION['language'] = 'en';
        $this->assertEquals(
            '<html prefix="og: http://ogp.me/ns#" lang="en">',
            Template::getOgHTMLPrefix()
        );
        $_SESSION['language'] = 'de';
        $this->assertEquals(
            '<html prefix="og: http://ogp.me/ns#" lang="de">',
            Template::getOgHTMLPrefix()
        );
        unset($_SESSION['language']);
    }

    public function testGetBaseMetas(): void {
        $baseMetas = Template::getBaseMetas();
        $this->assertTrue(
            str_contains(
                $baseMetas,
                '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>'
            )
        );
        $this->assertTrue(
            str_contains(
                $baseMetas,
                '<meta charset="utf-8"/>'
            )
        );
    }

    public function testGetBaseMetasDisableNoFormatDetection(): void {
        Settings::set('disable_no_format_detection', '1');
        $expected = '<meta name="format-detection" content="telephone=no"/>';

        $baseMetas = Template::getBaseMetas();
        $this->assertFalse(str_contains($baseMetas, $expected));

        Settings::delete('disable_no_format_detection');
        $baseMetas = Template::getBaseMetas();
        $this->assertTrue(str_contains($baseMetas, $expected));
    }

    public function testGetSiteSloganWithoutLanguage(): void {
        $_SESSION['language'] = 'de';
        $this->assertEquals('SiteSlogan Deutsch', Template::getSiteSlogan());

        $_SESSION['language'] = 'en';
        $this->assertEquals('SiteSlogan English', Template::getSiteSlogan());
    }

    public function testGetMottoWithoutLanguage(): void {
        $_SESSION['language'] = 'de';
        $this->assertEquals('SiteSlogan Deutsch', Template::getMotto());
    }

    public function testMottoWithoutLanguage(): void {
        $_SESSION['language'] = 'de';

        ob_start();
        Template::motto();
        $this->assertEquals('SiteSlogan Deutsch', ob_get_clean());
    }

    public function testGetSiteSloganWithExistingLanguage(): void {
        $_SESSION['language'] = 'fr';
        $this->assertEquals('SiteSlogan General', Template::getSiteSlogan());
    }

    public function testGetSiteSloganWithNotExistingLanguage(): void {
        $this->assertEquals('SiteSlogan General', Template::getSiteSlogan());
    }

    public function testGetjQueryScript(): void {
        $file = 'node_modules/jquery/dist/jquery.min.js';
        $time = File::getLastChanged($file);
        $expected = '<script src="' . $file . '?time=' . $time .
                '"></script>';
        $this->assertStringContainsString($expected, Template::getjQueryScript());
    }

    public function testGetContentReturnsContent(): void {
        $_GET['slug'] = 'lorem_ipsum';
        $_SESSION['language'] = 'de';
        $_GET['REQUEST_URI'] = '/lorem_ipsum.html';
        $content = Template::getContent();

        $this->assertStringContainsString(
            'Lorem ipsum dolor sit amet, ' .
            'consetetur sadipscing elitr',
            $content
        );
    }

    public function testContentOutputsContent(): void {
        $_GET['slug'] = 'lorem_ipsum';
        $_SESSION['language'] = 'de';
        $_GET['REQUEST_URI'] = '/lorem_ipsum.html';

        ob_start();
        Template::content();
        $content = ob_get_clean();

        $this->assertStringContainsString(
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr',
            $content
        );
    }

    public function testGetContentReturnsNotFound(): void {
        $_GET['slug'] = 'gibts_nicht';
        $_SESSION['language'] = 'de';
        $_GET['REQUEST_URI'] = '/gibts_nicht.html';

        $content = Template::getContent();

        $this->assertStringContainsString(
            "This page doesn't exist.",
            $content
        );
    }

    public function testGetLanguageSelection(): void {
        $html = Template::_getLanguageSelection();
        $this->assertTrue(
            str_contains(
                $html,
                "<ul class='language_selection'>",
            )
        );

        // By default there should be at least 2 languages
        // german and english
        $this->assertGreaterThanOrEqual(2, substr_count($html, '<li>'));
        // TODO: Check if there are links in the returned html
    }

    public function testGetLanguageSelectionWithDomain2LanguageMapping(): void {
        $mappingLines = [
            'example.de=>de',
            'example.co.uk=>en'
        ];

        Settings::set(
            'domain_to_language',
            implode("\n", $mappingLines)
        );
        $html = Template::_getLanguageSelection();
        $this->assertTrue(
            str_contains(
                $html,
                "<ul class='language_selection'>"
            )
        );

        // By default there should be at least 2 languages
        // german and english
        $this->assertGreaterThanOrEqual(2, substr_count($html, '<li>'));

        $this->assertStringContainsString('://example.de', $html);
        $this->assertStringContainsString('://example.co.uk', $html);
    }

    public function testGetHomepageOwner(): void {
        Settings::set('homepage_owner', 'John Doe');
        $this->assertEquals('John Doe', Template::getHomepageOwner());
    }

    public function testHomepageOwner(): void {
        Settings::set('homepage_owner', 'John Doe');

        ob_start();
        Template::homepageOwner();

        $this->assertEquals('John Doe', ob_get_clean());
    }

    public function testGetFooterText(): void {
        Settings::set('footer_text', '&copy; (C) [year] by John Doe');

        $year = date('Y');

        $this->assertEquals(
            "&copy; (C) {$year} by John Doe",
            Template::getFooterText()
        );
    }

    public function testFooter(): void {
        ob_start();
        Template::footer();
        $html = ob_get_clean();
        $this->assertStringContainsString('<script src', $html);
        $this->assertStringContainsString('.js?time=', $html);
    }

    public function testFooterText(): void {
        Settings::set('footer_text', '&copy; (C) [year] by John Doe');

        $year = date('Y');

        ob_start();
        Template::footerText();
        $this->assertEquals(
            "&copy; (C) {$year} by John Doe",
            ob_get_clean()
        );
    }

    public function testGetContentWithPlaceholder(): void {
        $manager = new \App\Models\Users\UserManager();
        $users = $manager->getAllUsers();
        $user = $users[0];
        $user_id = $user->getId();

        $groups = Group::getAll();
        $group = $groups[0];
        $group_id = $group->getId();

        $page = new Page();
        $page->title = 'Test Page ' . time();
        $page->slug = 'test-page-' . time();
        $page->language = 'de';
        $page->menu = 'not_in_menu';
        $page->content = '<p>Wir schreiben das Jahr [year] des fliegenden ' .
                'Spaghettimonsters</p>';
        $page->author_id = $user_id;
        $page->group_id = $group_id;
        $page->save();

        $_SESSION['language'] = $page->language;
        $_GET['slug'] = $page->slug;

        $_GET['REQUEST_URI'] = "/{$page->slug}.html";
        $this->assertEquals(
            '<p>Wir schreiben das Jahr ' . date('Y') .
            ' des fliegenden Spaghettimonsters</p>',
            Template::getContent()
        );
    }

    public function testGetBodyClassesHome(): void {
        $_SESSION['language'] = 'de';
        $_GET['slug'] = get_frontpage();
        $this->assertMatchesRegularExpression(
            '/page-id-\d+ home page(.+)/',
            Template::getBodyClasses()
        );

        \App\Storages\Vars::delete('id');
        \App\Storages\Vars::delete('active');
    }

    public function testBodyClassesHome(): void {
        $_SESSION['language'] = 'de';
        $_GET['slug'] = get_frontpage();

        ob_start();
        Template::bodyClasses();

        $this->assertMatchesRegularExpression(
            '/page-id-\d+ home page(.+)/',
            ob_get_clean()
        );

        \App\Storages\Vars::delete('id');
        \App\Storages\Vars::delete('active');
    }

    public function testGetBodyClassesError403Active(): void {
        $page = new Page();
        $page->title = 'testgetbodyclasses';
        $page->slug = 'testpage-' . uniqid();
        $page->language = 'de';
        $page->content = 'Hello World';
        $page->author_id = 1;
        $page->group_id = 1;
        $page->access = 'all';
        $page->active = 0;
        $page->save();

        $_SESSION['language'] = $page->language;
        $_GET['slug'] = $page->slug;

        $this->assertMatchesRegularExpression(
            '/page-id-\d+ error403 errorPage(.+)/',
            Template::getBodyClasses()
        );

        $page->delete();

        \App\Storages\Vars::delete('id');
        \App\Storages\Vars::delete('active');
    }

    public function testGetBodyClassesError403CauseAccess(): void {
        $page = new Page();
        $page->title = 'testgetbodyclasses';
        $page->slug = 'testpage-' . uniqid();
        $page->language = 'de';
        $page->content = 'Hello World';
        $page->author_id = 1;
        $page->group_id = 1;
        $page->access = (string)PHP_INT_MAX;
        $page->active = 1;
        $page->save();

        $_SESSION['language'] = $page->language;
        $_GET['slug'] = $page->slug;

        $this->assertMatchesRegularExpression(
            '/page-id-\d+ error403 errorPage(.+)/',
            Template::getBodyClasses()
        );

        $page->delete();

        \App\Storages\Vars::delete('id');
        \App\Storages\Vars::delete('active');
    }

    public function testGetBodyClassesError404(): void {
        $_SESSION['language'] = 'de';
        $_GET['slug'] = 'gibts-nicht';
        $this->assertMatchesRegularExpression(
            '/error404 errorPage(.+)/',
            Template::getBodyClasses()
        );

        \App\Storages\Vars::delete('id');
        \App\Storages\Vars::delete('active');
    }

    public function testGetBodyClassesMobile(): void {
        $_SESSION['language'] = 'de';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0' .
                ' like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) ' .
                'Version/5.1 Mobile/9A334 Safari/7534.48.3';
        $this->assertStringContainsString(
            'mobile',
            Template::getBodyClasses()
        );
        $this->assertStringNotContainsString(
            'desktop',
            Template::getBodyClasses()
        );

        \App\Storages\Vars::delete('id');
        \App\Storages\Vars::delete('active');
    }

    public function testGetBodyClassesDesktop(): void {
        $_SESSION['language'] = 'de';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 6.1;' .
                ' Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)' .
                ' Chrome/63.0.3239.132 Safari/537.36';
        $this->assertStringContainsString(
            'desktop',
            Template::getBodyClasses()
        );
        $this->assertStringNotContainsString(
            'mobile',
            Template::getBodyClasses()
        );

        \App\Storages\Vars::delete('id');
        \App\Storages\Vars::delete('active');
    }

    public function testGetBodyClassesContainsModule(): void {
        $_SESSION['language'] = 'de';
        $_GET['slug'] = \App\Helpers\ModuleHelper::getFirstPageWithModule()->slug;
        $this->assertMatchesRegularExpression(
            '/page-id-\d+ (.+)containsModule/',
            Template::getBodyClasses()
        );
        \App\Storages\Vars::delete('id');
        \App\Storages\Vars::delete('active');
    }

    public function testGetDocType(): void {
        $this->assertEquals('<!doctype html>', Template::getDoctype());
    }

    public function testDocType(): void {
        ob_start();
        Template::doctype();
        $this->assertEquals('<!doctype html>', ob_get_clean());
    }

    public function testOgHTMLPrefix(): void {
        $_SESSION['language'] = 'en';

        ob_start();
        Template::OgHTMLPrefix();
        $this->assertEquals(
            '<html prefix="og: http://ogp.me/ns#" lang="en">',
            ob_get_clean()
        );

        $_SESSION['language'] = 'de';
        ob_start();
        Template::OgHTMLPrefix();
        $this->assertEquals(
            '<html prefix="og: http://ogp.me/ns#" lang="de">',
            ob_get_clean()
        );
        unset($_SESSION['language']);
    }

    public function testGetBaseMetasForNonExistingPage(): void {
        $_GET['slug'] = 'gibts_echt_nicht';
        $_SESSION['language'] = 'de';

        $this->assertNotEmpty(Template::getBaseMetas());
    }

    public function testEditButtonNotLoggedIn(): void {
        ob_start();
        Template::editButton();
        $this->assertEmpty(ob_get_clean());
    }

    public function testGetHeadlineNotFound(): void {
        $_GET['slug'] = 'gibts_echt_nicht';
        $_SESSION['language'] = 'de';

        $this->assertEquals(
            '<h2>Page not found</h2>',
            Template::getHeadline('<h2>%title%</h2>')
        );
    }

    public function testGetHeadlineReturnsNull(): void {
        $manager = new \App\Models\Users\UserManager();
        $users = $manager->getAllUsers();
        $user = $users[0];
        $user_id = $user->getId();

        $groups = Group::getAll();
        $group = $groups[0];
        $group_id = $group->getId();

        $page = new Page();
        $page->title = 'Test Page ' . time();
        $page->slug = 'test-page-' . time();
        $page->language = 'de';
        $page->menu = 'not_in_menu';
        $page->content = '<p>Wir schreiben das Jahr [year] des fliegenden ' .
                'Spaghettimonsters</p>';
        $page->author_id = $user_id;
        $page->group_id = $group_id;
        $page->show_headline = false;
        $page->save();

        $_GET['slug'] = $page->slug;
        $_SESSION['language'] = $page->language;

        $this->assertNull(Template::getHeadline());
    }

    public function testGetHeadlineReturnsTitle(): void {
        $manager = new \App\Models\Users\UserManager();
        $users = $manager->getAllUsers();
        $user = $users[0];
        $user_id = $user->getId();

        $groups = Group::getAll();
        $group = $groups[0];
        $group_id = $group->getId();

        $page = new Page();
        $page->title = 'Titel';
        $page->slug = 'test-page-' . time();
        $page->language = 'de';
        $page->menu = 'not_in_menu';
        $page->content = '<p>Wir schreiben das Jahr [year] des fliegenden ' .
                'Spaghettimonsters</p>';
        $page->author_id = $user_id;
        $page->group_id = $group_id;
        $page->save();

        $_GET['slug'] = $page->slug;
        $_SESSION['language'] = $page->language;

        $this->assertEquals('<h1>Titel</h1>', Template::getHeadline());
    }

    public function testGetHeadlineReturnsHeadline(): void {
        $manager = new \App\Models\Users\UserManager();
        $users = $manager->getAllUsers();
        $user = $users[0];
        $user_id = $user->getId();

        $groups = Group::getAll();
        $group = $groups[0];
        $group_id = $group->getId();

        $page = new Page();
        $page->title = 'Titel';
        $page->alternate_title = 'Alternative Überschrift';
        $page->slug = 'test-page-' . time();
        $page->language = 'de';
        $page->menu = 'not_in_menu';
        $page->content = '<p>Wir schreiben das Jahr [year] des fliegenden ' .
                'Spaghettimonsters</p>';
        $page->author_id = $user_id;
        $page->group_id = $group_id;
        $page->save();

        $_GET['slug'] = $page->slug;
        $_SESSION['language'] = $page->language;

        $this->assertEquals(
            '<h1>Alternative Überschrift</h1>',
            Template::getHeadline()
        );
    }

    public function testHeadlinePrintsString(): void {
        $pages = ContentFactory::getAllRegular();

        $first = $pages[0];

        $_GET['slug'] = $first->slug;
        $_SESSION['language'] = $first->language;

        ob_start();
        Template::headline('<h3>%title%</h3>');

        $this->assertEquals("<h3>{$first->title}</h3>", ob_get_clean());
    }

    public function testComments(): void {
        $_GET['slug'] = 'gibts_echt_nicht';
        $_SESSION['language'] = 'de';

        ob_start();
        Template::comments();
        $this->assertEmpty(ob_get_clean());
    }

    public function testGetCommentsReturnsHtml(): void {
        $page = $this->getPageWithCommentsEnabled();

        $_GET['slug'] = $page->slug;
        $_SESSION['language'] = $page->language;

        $commentsController = ControllerRegistry::get('CommentsController');
        $commentsController->beforeHtml();

        $html = Template::getComments();
        $this->assertTrue(stringContainsHtml($html));

        $this->assertStringContainsString('Send comment', $html);
        $this->assertStringContainsString('Your E-Mail Address', $html);
    }

    public function testGetCommentsReturnsEmptyString(): void {
        $_GET['slug'] = 'gibts_echt_nicht';
        $_SESSION['language'] = 'de';

        $this->assertEmpty(Template::getComments());
    }

    public function testExecuteDefaultOrOwnTemplateOwnExists(): void {
        $this->assertNotEmpty(Template::executeDefaultOrOwnTemplate('bottom.php'));
    }

    public function testExecuteDefaultOrOwnTemplateWithNonExistingFile(): void {
        $this->expectException(FileNotFoundException::class);
        Template::executeDefaultOrOwnTemplate('gibts_echt_nicht');
    }

    public function testExecuteModuleTemplateWithNonExisting(): void {
        $this->expectException(FileNotFoundException::class);
        Template::executeModuleTemplate(
            'fortune2',
            'gibts_echt_nicht'
        );
    }

    public function testEscape(): void {
        $input = "Hello <script>alert('xss')";
        $expected = 'Hello &lt;script&gt;alert(&#039;xss&#039;)';

        ob_start();
        Template::escape($input);
        $this->assertEquals($expected, ob_get_clean());
    }

    public function testLogoDisabled(): void {
        $actual = TestHelper::getOutput(static function(): void {
            Settings::set('logo_disabled', 'yes');
            Template::logo();
        });

        $this->assertEmpty($actual);
    }

    private function setSiteSlogan(): void {
        Settings::set('site_slogan', 'SiteSlogan General');
        Settings::set('site_slogan_de', 'SiteSlogan Deutsch');
        Settings::set('site_slogan_en', 'SiteSlogan English');
        Settings::delete('site_slogan_fr');
    }

    private function getPageWithCommentsEnabled() {
        $manager = new \App\Models\Users\UserManager();
        $users = $manager->getAllUsers();
        $user = $users[0];
        $user_id = $user->getId();

        $page = new Page();
        $page->title = 'Test Page ' . time();
        $page->slug = 'test-page-' . time();
        $page->language = 'de';
        $page->menu = 'not_in_menu';
        $page->content = '<p>Wir schreiben das Jahr [year] des fliegenden ' .
                'Spaghettimonsters</p>';

        $user_id = $user->getId();
        $groups = Group::getAll();
        $group = $groups[0];
        $group_id = $group->getId();

        $page->author_id = $user_id;
        $page->group_id = $group_id;
        $page->comments_enabled = 1;

        $page->save();

        return $page;
    }
}
