<?php

class ResponseTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $_SERVER["HTTP_HOST"] = "ulicms.de";
        $_SERVER["REQUEST_URI"] = "/";
        $_SESSION["language"] = "en";
    }

    public function tearDown()
    {
        unset($_SERVER["HTTP_HOST"]);
        unset($_SERVER["REQUEST_URI"]);
        unset($_SESSION["language"]);
    }

    public function testGetStatusCodeByNumber()
    {
        $this->assertEquals("200 OK", Response::getStatusCodeByNumber(200));
        $this->assertEquals("301 Moved Permanently", Response::getStatusCodeByNumber(301));
        $this->assertEquals("302 Found", Response::getStatusCodeByNumber(302));
        $this->assertEquals("401 Unauthorized", Response::getStatusCodeByNumber(401));
        $this->assertEquals("403 Forbidden", Response::getStatusCodeByNumber(403));
        $this->assertEquals("404 Not Found", Response::getStatusCodeByNumber(404));
        $this->assertEquals('418 I\'m a teapot', Response::getStatusCodeByNumber(418));
    }

    public function testGetSafeRedirectURL()
    {
        $this->assertEquals("http://ulicms.de/lorem_ipsum.html", Response::getSafeRedirectURL("http://ulicms.de/lorem_ipsum.html"));
        
        $this->assertEquals("http://ulicms.de/welcome.html", Response::getSafeRedirectURL("https://google.de"));
        $this->assertEquals("https://google.de", Response::getSafeRedirectURL("https://google.de", array(
            "google.de"
        )));        
        
    }
}