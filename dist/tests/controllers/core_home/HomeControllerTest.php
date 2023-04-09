<?php

use App\Constants\HtmlEditor;

class HomeControllerTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        require_once getLanguageFilePath('en');
        Settings::set('installed_at', '1495362918');

        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = '443';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/foobar/foo.html';
    }

    protected function tearDown(): void
    {
        Database::deleteFrom(
            'users',
            "username like 'online-%' or username like 'nicht-online-%'"
        );
        $_SERVER = [];
    }

    public function testGetModel()
    {
        $controller = new HomeController();
        $model = $controller->getModel();

        $this->assertInstanceOf(HomeViewModel::class, $model);

        // TODO: Do more asserts, check data
    }

    public function testNewsfeed()
    {
        $controller = new HomeController();
        $html = $controller->_newsfeed();

        $this->assertGreaterThanOrEqual(
            5,
            substr_count(
                $html,
                '<a href'
            )
        );
        $this->assertGreaterThanOrEqual(
            5,
            substr_count(
                $html,
                'ulicms.de/?single='
            )
        );
    }

    public function testTopPages()
    {
        $controller = new HomeController();
        $html = $controller->_topPages();
        $this->assertEquals(
            6,
            substr_count(
                $html,
                '<tr'
            )
        );
    }

    public function testLastUpdatedPages()
    {
        $controller = new HomeController();
        $html = $controller->_lastUpdatedPages();
        $this->assertEquals(
            6,
            substr_count(
                $html,
                '<tr'
            )
        );
    }

    public function testOnlineUsers()
    {
        $this->createTestUsers();

        $controller = new HomeController();
        $output = $controller->_onlineUsers();

        $this->assertStringContainsString('online-1', $output);
        $this->assertStringContainsString('online-2', $output);
        $this->assertStringNotContainsString('nicht-online-', $output);
        $this->assertGreaterThanOrEqual(
            2,
            substr_count($output, 'content/avatars/')
        );
    }

    public function testStatistics()
    {
        $usersCount = count(getUsers());
        $pagesCount = count(ContentFactory::getAll());

        $controller = new HomeController();
        $output = $controller->_statistics();

        $this->assertStringContainsString('Site online since', $output);
        $this->assertStringContainsString('Count of pages', $output);
        $this->assertStringContainsString('Amount of Users', $output);
        $this->assertStringContainsString("<td>$usersCount</td>", $output);
        $this->assertStringContainsString("<td>$pagesCount</td>", $output);
    }

    protected function createTestUsers()
    {
        $this->createOnlineUsers();
        $this->createOfflineUsers();
    }

    protected function createOnlineUsers()
    {
        $user1 = new User();
        $user1->setUsername('online-2');
        $user1->setPassword(rand_string(23));
        $user1->setLastname('Beutlin');
        $user1->setFirstname('Bilbo');
        $user1->setHTMLEditor(HtmlEditor::CKEDITOR);
        $user1->save();
        $user1->setLastAction(time() - 10);

        $user2 = new User();
        $user2->setUsername('online-1');
        $user2->setPassword(rand_string(23));
        $user2->setLastname('Duck');
        $user2->setFirstname('Donald');
        $user2->setHTMLEditor(HtmlEditor::CKEDITOR);
        $user2->save();
        $user2->setLastAction(time() - 10);
    }

    protected function createOfflineUsers()
    {
        $user3 = new User();
        $user3->setUsername('nicht-online-1');
        $user3->setPassword(rand_string(23));
        $user3->setLastname('Duck');
        $user3->setFirstname('Donald');
        $user3->setHTMLEditor(HtmlEditor::CKEDITOR);
        $user3->save();
        $user3->setLastAction(300);

        $user4 = new User();
        $user4->setUsername('nicht-online-2');
        $user4->setPassword(rand_string(23));
        $user4->setLastname('Duck');
        $user4->setFirstname('Donald');
        $user4->setHTMLEditor(HtmlEditor::CKEDITOR);
        $user4->save();
    }
}
