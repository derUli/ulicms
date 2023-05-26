<?php

use App\Translations\Translation;

class CoreFormsControllerTest extends \PHPUnit\Framework\TestCase {
    private $defaultSettings = [];

    protected function setUp(): void {
        $this->defaultSettings = [
            'country_blacklist' => Settings::get('country_blacklist'),
            'reject_requests_from_bots' => Settings::get('reject_requests_from_bots'),
            'disallow_chinese_chars' => Settings::get('chinese_chars_not_allowed'),
            'disallow_cyrillic_chars' => Settings::get('disallow_cyrillic_chars'),
            'disallow_rtl_chars' => Settings::get('disallow_rtl_chars')
        ];
        require_once getLanguageFilePath('en');
        Translation::loadAllModuleLanguageFiles('en');
    }

    protected function tearDown(): void {
        $_POST = [];
        $_SERVER = [];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testIncSpamCount(): void {
        $controller = new CoreFormsController();
        $initialCount = (int)Settings::get('contact_form_refused_spam_mails');

        for ($i = 1; $i <= 3; $i++) {
            $oldCount = (int)Settings::get('contact_form_refused_spam_mails');
            $newCount = $controller->_incSpamCount();
            $this->assertIsInt($newCount);
            $this->assertGreaterThan($oldCount, $newCount);
        }

        $this->assertEquals(
            $initialCount + 3,
            (int)Settings::get('contact_form_refused_spam_mails')
        );
    }

    public function testSpamCheckReturnsNull(): void {
        $_POST = [
            'foo' => 'bar',
            'hello' => 'world'
        ];
        $controller = new CoreFormsController();
        $this->assertNull($controller->_spamCheck());
    }

    public function testSpamCheckWithHoneypotFilled(): void {
        $_POST = [
            'foo' => 'bar',
            'hello' => 'world',
            'my_homepage_url' => 'http://hacker.ru'
        ];
        $controller = new CoreFormsController();
        $this->assertEquals('Honeypot is not empty!', $controller->_spamCheck());
    }

    public function testWithBadWord(): void {
        $_POST = [
            'foo' => 'bar',
            'hello' => 'world',
            'spam' => 'Viagra'
        ];
        $controller = new CoreFormsController();
        $this->assertEquals('The request contains a bad word: viagra', $controller->_spamCheck());
    }

    public function testWithForbiddenCountry(): void {
        Settings::set('country_blacklist', 'vn,jp,at,tr');

        $_POST = [
            'foo' => 'bar',
            'hello' => 'world'
        ];
        $_SERVER['REMOTE_ADDR'] = '123.30.54.106';
        $controller = new CoreFormsController();
        $this->assertStringContainsString(
            'Access to this function for your country is blocked',
            $controller->_spamCheck()
        );
    }

    public function testWithBot(): void {
        Settings::set('reject_requests_from_bots', '1');

        $_POST = [
            'foo' => 'bar',
            'hello' => 'world'
        ];
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';

        $controller = new CoreFormsController();
        $this->assertStringContainsString(
            'Bots are not allowed to send messages on this website.',
            $controller->_spamCheck()
        );
    }

    public function testWithChinese(): void {
        Settings::set('disallow_chinese_chars', '1');

        $_POST = [
            'foo' => 'Test 習近平是小熊維尼 Test',
            'hello' => 'world'
        ];

        $controller = new CoreFormsController();
        $this->assertStringContainsString(
            'Chinese chars are not allowed!',
            $controller->_spamCheck()
        );
    }

    public function testWithRTL(): void {
        Settings::set('disallow_rtl_chars', '1');

        $_POST = [
            'foo' => 'من آرزو می کنم رئیس جمهور ایران کرونا',
            'hello' => 'world'
        ];

        $controller = new CoreFormsController();
        $this->assertStringContainsString(
            'Right-To-Left languages are not allowed!',
            $controller->_spamCheck()
        );
    }

    public function testWithCyrillic(): void {
        Settings::set('disallow_cyrillic_chars', '1');

        $_POST = [
            'foo' => 'Путин воняет',
            'hello' => 'world'
        ];

        $controller = new CoreFormsController();
        $this->assertStringContainsString(
            'Cyrillic chars are not allowed!',
            $controller->_spamCheck()
        );
    }
}
