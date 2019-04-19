<?php

class RequestTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        unset($_SERVER["HTTP_HOST"]);
        unset($_SERVER["HTTP_REFERRER"]);
        unset($_SERVER["HTTP_USER_AGENT"]);
        unset($_SERVER["REQUEST_URI"]);
    }

    public function testGetVar() {
        $_POST["var1"] = "this";
        $_GET["var1"] = "that";
        $_GET["var2"] = "123";
        $_POST["var3"] = "1.5";
        $this->assertEquals("this", Request::getVar("var1"));
        $this->assertEquals("this", Request::getVar("var1"));
        $this->assertEquals(null, Request::getVar("nothing"));
        $this->assertEquals("not text", Request::getVar("nothing", "not text"));

        $this->assertEquals(0, Request::getVar("var1", null, "int"));
        $this->assertEquals(0.0, Request::getVar("var1", null, "float"));

        $this->assertEquals(123.0, Request::getVar("var2", null, "float"));
        $this->assertEquals(1, Request::getVar("var3", null, "int"));
    }

    public function testGetMethod() {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $this->assertEquals("get", Request::getMethod());
        $this->assertTrue(Request::isGet());
        $this->assertFalse(Request::isPost());
        $this->assertFalse(Request::isHead());
        $_SERVER["REQUEST_METHOD"] = "POST";
        $this->assertEquals("post", Request::getMethod());
        $this->assertFalse(Request::isGet());
        $this->assertTrue(Request::isPost());
        $this->assertFalse(Request::isHead());
        $_SERVER["REQUEST_METHOD"] = "HEAD";
        $this->assertEquals("head", Request::getMethod());
        $this->assertFalse(Request::isGet());
        $this->assertFalse(Request::isPost());
        $this->assertTrue(Request::isHead());
    }

    public function testIsAjaxRequest() {
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->assertFalse(Request::isAjaxRequest());
        $_SERVER['HTTP_X_REQUESTED_WITH'] = "XMLHttpRequest";
        $this->assertTrue(Request::isAjaxRequest());
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    public function testGetDomain() {
        $_SERVER["HTTP_HOST"] = "example.org";
        $this->assertEquals("example.org", Request::getDomain());
        $this->assertEquals("example.org", get_domain());

        $_SERVER["HTTP_HOST"] = "en.ulicms.de";
        $this->assertEquals("en.ulicms.de", Request::getDomain());
        $this->assertEquals("en.ulicms.de", get_domain());
    }

    public function testGetReferrer() {
        $_SERVER["HTTP_REFERER"] = "https://www.google.de/?q=Hallo%20Welt";
        $this->assertEquals($_SERVER["HTTP_REFERER"], Request::getReferrer());
        $this->assertEquals($_SERVER["HTTP_REFERER"], get_referer());
        $this->assertEquals($_SERVER["HTTP_REFERER"], get_referrer());
    }

    public function testGetUserAgent() {
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36";
        $this->assertEquals($_SERVER["HTTP_USER_AGENT"], Request::getUserAgent());
        $this->assertEquals($_SERVER["HTTP_USER_AGENT"], get_useragent());
    }

    public function testGetRequestUri() {
        $_SERVER["REQUEST_URI"] = "/admin/index.php?action=foobar";
        $this->assertEquals($_SERVER["REQUEST_URI"], Request::getRequestUri());
        $this->assertEquals($_SERVER["REQUEST_URI"], get_request_uri());
    }

}
