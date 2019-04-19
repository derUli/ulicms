<?php

class UrlHelperTest extends \PHPUnit\Framework\TestCase
{

    public function testGetUrlWithoutGetParameters()
    {
        $this->assertEquals("http://www.ulicms.de/", UrlHelper::getUrlWithoutGetParameters("http://www.ulicms.de/?foo=bar&hello=world"));
        $this->assertEquals("http://www.ulicms.de/index.html", UrlHelper::getUrlWithoutGetParameters("http://www.ulicms.de/index.html?foo=bar&hello=world"));
        $this->assertEquals("http://www.ulicms.de:8080/index.html", UrlHelper::getUrlWithoutGetParameters("http://www.ulicms.de:8080/index.html?foo=bar&hello=world"));
        $this->assertEquals("http://www.ulicms.de:8080", UrlHelper::getUrlWithoutGetParameters("http://www.ulicms.de:8080"));
        $this->assertEquals("https://www.ulicms.de/", UrlHelper::getUrlWithoutGetParameters("https://www.ulicms.de/?foo=bar&hello=world"));
        $this->assertEquals("https://www.ulicms.de/index.html", UrlHelper::getUrlWithoutGetParameters("https://www.ulicms.de/index.html?foo=bar&hello=world"));
        $this->assertEquals("https://www.ulicms.de:8080/index.html", UrlHelper::getUrlWithoutGetParameters("https://www.ulicms.de:8080/index.html?foo=bar&hello=world"));
        $this->assertEquals("https://www.ulicms.de:8080", UrlHelper::getUrlWithoutGetParameters("https://www.ulicms.de:8080"));
    }
}