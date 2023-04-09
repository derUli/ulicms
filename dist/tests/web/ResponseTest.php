<?php

use App\Helpers\TestHelper;

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        require_once getLanguageFilePath('en');
        $_SERVER['HTTP_HOST'] = 'ulicms.de';
        $_SERVER['REQUEST_URI'] = '/';
        $_SESSION['language'] = 'en';
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['REQUEST_URI']);
        unset($_SESSION['language']);
    }

    public function testGetStatusCodeByNumber()
    {
        $this->assertEquals('200 OK', Response::getStatusCodeByNumber(200));
        $this->assertEquals('301 Moved Permanently', Response::getStatusCodeByNumber(301));
        $this->assertEquals('302 Found', Response::getStatusCodeByNumber(302));
        $this->assertEquals('401 Unauthorized', Response::getStatusCodeByNumber(401));
        $this->assertEquals('403 Forbidden', Response::getStatusCodeByNumber(403));
        $this->assertEquals('404 Not Found', Response::getStatusCodeByNumber(404));
        $this->assertEquals('418 I\'m a teapot', Response::getStatusCodeByNumber(418));
    }

    public function testGetSafeRedirectURL()
    {
        $this->assertEquals('http://ulicms.de/lorem_ipsum.html', Response::getSafeRedirectURL('http://ulicms.de/lorem_ipsum.html'));

        $this->assertEquals('http://ulicms.de/welcome', Response::getSafeRedirectURL('https://google.de'));

        $this->assertEquals('https://google.de', Response::getSafeRedirectURL('https://google.de', [
                    'google.de'
        ]));
    }

    public function testJavascriptRedirect()
    {
        $expected = file_get_contents(
            Path::resolve('tests/fixtures/javascriptRedirect.expected.txt')
        );

        $actual = TestHelper::getOutput(function () {
            Response::javascriptRedirect('https://google.de');
        });

        $this->assertEquals($expected, $actual);
    }
}
