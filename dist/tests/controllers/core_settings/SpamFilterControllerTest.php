<?php

class SpamFilterControllerTest extends \PHPUnit\Framework\TestCase
{
    private $defaultSettings = [];

    protected function setUp(): void
    {
        $this->defaultSettings = [
            'spamfilter_enabled' => Settings::get('spamfilter_enabled'),
            'country_blacklist' => Settings::get('country_blacklist'),
            'spamfilter_words_blacklist' => Settings::get('spamfilter_words_blacklist'),
            'disallow_chinese_chars' => Settings::get('disallow_chinese_chars'),
            'disallow_cyrillic_chars' => Settings::get('disallow_cyrillic_chars'),
            'disallow_rtl_chars' => Settings::get('disallow_rtl_chars'),
            'reject_requests_from_bots' => Settings::get('reject_requests_from_bots'),
            'check_mx_of_mail_address' => Settings::get('check_mx_of_mail_address'),
            'min_time_to_fill_form' => Settings::get('min_time_to_fill_form'),
        ];
    }

    protected function tearDown(): void
    {
        $_POST = [];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testSavePostWithValues(): void
    {
        $_POST = [
            'spamfilter_enabled' => 'yes',
            'country_blacklist' => 'de,en',
            'spamfilter_words_blacklist' => "foo\nbar",
            'disallow_chinese_chars' => '1',
            'disallow_cyrillic_chars' => '1',
            'disallow_rtl_chars' => '1',
            'reject_requests_from_bots' => '1',
            'check_mx_of_mail_address' => '1',
            'min_time_to_fill_form' => '3'
        ];

        Settings::set('default_language', 'en');

        $controller = new SpamFilterController();
        $controller->_savePost();

        $this->assertEquals(
            'yes',
            Settings::get('spamfilter_enabled')
        );
        $this->assertEquals(
            'de,en',
            Settings::get('country_blacklist')
        );
        $this->assertEquals(
            "foo\nbar",
            Settings::get('spamfilter_words_blacklist')
        );
        $this->assertEquals(
            'disallow',
            Settings::get('disallow_chinese_chars')
        );
        $this->assertEquals(
            'disallow',
            Settings::get('disallow_cyrillic_chars')
        );
        $this->assertEquals(
            'disallow',
            Settings::get('disallow_rtl_chars')
        );
        $this->assertEquals(
            '1',
            Settings::get('reject_requests_from_bots')
        );
        $this->assertEquals(
            '1',
            Settings::get('check_mx_of_mail_address')
        );
        $this->assertEquals(
            '3',
            Settings::get('min_time_to_fill_form')
        );
    }

    public function testSavePostWithoutValues(): void
    {
        $_POST = [
            'spamfilter_enabled' => 'no'
        ];

        Settings::set('default_language', 'en');

        $controller = new SpamFilterController();
        $controller->_savePost();

        $this->assertEquals(
            'no',
            Settings::get('spamfilter_enabled')
        );
        $this->assertEquals(
            'ru, cn, in',
            Settings::get('country_blacklist')
        );
        $this->assertStringContainsString(
            'enlargement',
            Settings::get('spamfilter_words_blacklist')
        );
        $this->assertNull(
            Settings::get('disallow_chinese_chars')
        );
        $this->assertNull(
            Settings::get('disallow_cyrillic_chars')
        );
        $this->assertNull(
            Settings::get('disallow_rtl_chars')
        );
        $this->assertNull(
            Settings::get('reject_requests_from_bots')
        );
        $this->assertNull(
            Settings::get('check_mx_of_mail_address')
        );
        $this->assertEquals(
            '0',
            Settings::get('min_time_to_fill_form')
        );
    }
}
