<?php

class RequestTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        unset($_SERVER["HTTP_HOST"]);
        unset($_SERVER["HTTP_REFERRER"]);
        unset($_SERVER["HTTP_USER_AGENT"]);
        unset($_SERVER["REQUEST_URI"]);
        unset($_SERVER["SERVER_PORT"]);
        unset($_SERVER["REMOTE_ADDR"]);
        unset($_SERVER["REMOTE_ADDR"]);
        unset($_SERVER["X_FORWARDED"]);
        unset($_SERVER["HTTP_X_FORWARDED_HOST"]);
    }

    public function testGetVar() {
        $_POST["var1"] = "this";
        $_GET["var1"] = "that";
        $_GET["var2"] = "123";
        $_POST["var3"] = "1.5";

        $_GET["var4"] = "true";
        $_GET["var5"] = "false";
        $_GET["var6"] = "3";

        $this->assertEquals("this", Request::getVar("var1"));
        $this->assertEquals("this", Request::getVar("var1"));
        $this->assertEquals(null, Request::getVar("nothing"));
        $this->assertEquals("not text", Request::getVar("nothing", "not text"));

        $this->assertEquals(0, Request::getVar("var1", null, "int"));
        $this->assertEquals(0.0, Request::getVar("var1", null, "float"));

        $this->assertEquals(123.0, Request::getVar("var2", null, "float"));
        $this->assertEquals(1, Request::getVar("var3", null, "int"));

        $this->assertEquals(1, Request::getVar("var4", null, "bool"));
        $this->assertEquals(0, Request::getVar("var5", null, "bool"));
        $this->assertEquals(1, Request::getVar("var6", null, "bool"));
    }

    public function testHasVarReturnsTrue() {
        $_POST["this_var"] = "exists";
        $this->assertTrue(Request::hasVar("this_var"));

        unset($_POST["this_var"]);
        $_GET["this_var"] = "exists";
        $this->assertTrue(Request::hasVar("this_var"));
    }

    public function testHasVarReturnsFalse() {
        $this->assertFalse(Request::hasVar("web_servers_are_magic"));
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

    public function testGetRequestMethod() {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $this->assertEquals("get", get_request_method());
        $this->assertTrue(Request::isGet());
        $this->assertFalse(Request::isPost());
        $this->assertFalse(Request::isHead());
        $_SERVER["REQUEST_METHOD"] = "POST";
        $this->assertEquals("post", get_request_method());
        $this->assertFalse(Request::isGet());
        $this->assertTrue(Request::isPost());
        $this->assertFalse(Request::isHead());
        $_SERVER["REQUEST_METHOD"] = "HEAD";
        $this->assertEquals("head", get_request_method());
        $this->assertFalse(Request::isGet());
        $this->assertFalse(Request::isPost());
        $this->assertTrue(Request::isHead());
    }

    public function testIsAjaxRequest() {
        unset($_SERVER["HTTP_X_REQUESTED_WITH"]);
        $this->assertFalse(Request::isAjaxRequest());
        $_SERVER["HTTP_X_REQUESTED_WITH"] = "XMLHttpRequest";
        $this->assertTrue(Request::isAjaxRequest());
        unset($_SERVER["HTTP_X_REQUESTED_WITH"]);
    }

    public function testGetDomain() {
        $_SERVER["HTTP_HOST"] = "example.org";
        $this->assertEquals("example.org", Request::getDomain());
        $this->assertEquals("example.org", get_domain());

        $_SERVER["HTTP_HOST"] = "en.ulicms.de";
        $this->assertEquals("en.ulicms.de", Request::getDomain());
        $this->assertEquals("en.ulicms.de", get_domain());
    }

    public function testGetStatusCodeByNumber() {
        $this->assertEquals("200 OK", getStatusCodeByNumber(200));
        $this->assertEquals("404 Not Found", getStatusCodeByNumber(404));
        $this->assertEquals("418 I'm a teapot", getStatusCodeByNumber(418));
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

    public function testIsSSLExpectTrue() {
        $_SERVER["SERVER_PORT"] = 443;
        $this->assertTrue(Request::isSSL());
    }

    public function testIsSSLExpectFalse() {
        $_SERVER["SERVER_PORT"] = 80;
        $this->assertFalse(Request::isSSL());
    }

    public function testGetPort() {
        $_SERVER["SERVER_PORT"] = 8080;
        $this->assertEquals(8080, Request::getPort());

        $_SERVER["SERVER_PORT"] = 443;
        $this->assertEquals(443, Request::getPort());
    }

    public function testGetProtocolExpectHttp() {
        $_SERVER["SERVER_PORT"] = 8080;
        $this->assertEquals("http://", Request::getProtocol());
    }

    public function testGetProtocolExpectHttps() {
        $_SERVER["SERVER_PORT"] = 443;
        $this->assertEquals("https://", Request::getProtocol());
    }

    public function testGetProtocolExpectHttpsWithPrefix() {
        $_SERVER["SERVER_PORT"] = 443;
        $this->assertEquals("https://www.ulicms.de", Request::getProtocol("www.ulicms.de"));
    }

    public function testGetIp() {
        $_SERVER["REMOTE_ADDR"] = "123.123.123.123";
        $this->assertEquals("123.123.123.123", Request::getIp());
    }

    public function testGetIpWithProxy() {
        $_SERVER["REMOTE_ADDR"] = "123.123.123.123";
        $_SERVER["X_FORWARDED"] = "111.111.111.111";

        $this->assertEquals("111.111.111.111", Request::getIp());
    }

    public function testSiteProtocolExpectHttp() {
        $_SERVER["SERVER_PORT"] = 80;
        ob_start();
        site_protocol();
        $this->assertEquals("http://", ob_get_clean());
    }

    public function testSiteProtocolExpectHttps() {
        $_SERVER["SERVER_PORT"] = 443;
        ob_start();
        site_protocol();
        $this->assertEquals("https://", ob_get_clean());
    }

    public function testIsSSLReturnsFalse() {
        $_SERVER["SERVER_PORT"] = 80;
        $this->assertFalse(is_ssl());
    }

    public function testIsSSLReturnsTrue() {
        $_SERVER["SERVER_PORT"] = 443;
        $this->assertTrue(is_ssl());
    }

}
