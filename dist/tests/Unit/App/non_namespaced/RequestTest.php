<?php


class RequestTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        require_once getLanguageFilePath('en');
        unset($_SERVER['HTTP_HOST'], $_SERVER['HTTP_REFERRER'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['REQUEST_URI'], $_SERVER['SERVER_PORT'], $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_ADDR'], $_SERVER['X_FORWARDED'], $_SERVER['HTTP_X_FORWARDED_HOST']);








    }

    public function testGetVar(): void {
        $_POST['var1'] = 'this';
        $_GET['var1'] = 'that';
        $_GET['var2'] = '123';
        $_POST['var3'] = '1.5';

        $_GET['var4'] = 'true';
        $_GET['var5'] = 'false';
        $_GET['var6'] = '3';

        $this->assertEquals('this', Request::getVar('var1'));
        $this->assertEquals('this', Request::getVar('var1'));
        $this->assertEquals(null, Request::getVar('nothing'));
        $this->assertEquals('not text', Request::getVar('nothing', 'not text'));

        $this->assertEquals(0, Request::getVar('var1', null, 'int'));
        $this->assertEquals(0.0, Request::getVar('var1', null, 'float'));

        $this->assertEquals(123.0, Request::getVar('var2', null, 'float'));
        $this->assertEquals(1, Request::getVar('var3', null, 'int'));

        $this->assertEquals(1, Request::getVar('var4', null, 'bool'));
        $this->assertEquals(0, Request::getVar('var5', null, 'bool'));
        $this->assertEquals(1, Request::getVar('var6', null, 'bool'));
    }

    public function testHasVarReturnsTrue(): void {
        $_POST['this_var'] = 'exists';
        $this->assertTrue(Request::hasVar('this_var'));

        unset($_POST['this_var']);
        $_GET['this_var'] = 'exists';
        $this->assertTrue(Request::hasVar('this_var'));
    }

    public function testHasVarReturnsFalse(): void {
        $this->assertFalse(Request::hasVar('web_servers_are_magic'));
    }

    public function testGetMethod(): void {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertEquals('get', Request::getMethod());
        $this->assertTrue(Request::isGet());
        $this->assertFalse(Request::isPost());
        $this->assertFalse(Request::isHead());
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertEquals('post', Request::getMethod());
        $this->assertFalse(Request::isGet());
        $this->assertTrue(Request::isPost());
        $this->assertFalse(Request::isHead());
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $this->assertEquals('head', Request::getMethod());
        $this->assertFalse(Request::isGet());
        $this->assertFalse(Request::isPost());
        $this->assertTrue(Request::isHead());
    }

    public function testGetRequestMethod(): void {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertEquals('get', get_request_method());
        $this->assertTrue(Request::isGet());
        $this->assertFalse(Request::isPost());
        $this->assertFalse(Request::isHead());
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertEquals('post', get_request_method());
        $this->assertFalse(Request::isGet());
        $this->assertTrue(Request::isPost());
        $this->assertFalse(Request::isHead());
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $this->assertEquals('head', get_request_method());
        $this->assertFalse(Request::isGet());
        $this->assertFalse(Request::isPost());
        $this->assertTrue(Request::isHead());
    }

    public function testIsAjaxRequest(): void {
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->assertFalse(Request::isAjaxRequest());
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue(Request::isAjaxRequest());
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    public function testGetDomain(): void {
        $_SERVER['HTTP_HOST'] = 'example.org';
        $this->assertEquals('example.org', Request::getDomain());
        $this->assertEquals('example.org', get_domain());

        $_SERVER['HTTP_HOST'] = 'en.ulicms.de';
        $this->assertEquals('en.ulicms.de', Request::getDomain());
        $this->assertEquals('en.ulicms.de', get_domain());
    }

    public function testGetReferrer(): void {
        $_SERVER['HTTP_REFERER'] = 'https://www.google.de/?q=Hallo%20Welt';
        $this->assertEquals($_SERVER['HTTP_REFERER'], Request::getReferrer());
        $this->assertEquals($_SERVER['HTTP_REFERER'], get_referrer());
    }

    public function testGetUserAgent(): void {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36';
        $this->assertEquals($_SERVER['HTTP_USER_AGENT'], Request::getUserAgent());
        $this->assertEquals($_SERVER['HTTP_USER_AGENT'], get_useragent());
    }

    public function testGetRequestUri(): void {
        $_SERVER['REQUEST_URI'] = '/admin/index.php?action=foobar';
        $this->assertEquals($_SERVER['REQUEST_URI'], Request::getRequestUri());
        $this->assertEquals($_SERVER['REQUEST_URI'], get_request_uri());
    }

    public function testIsSSLExpectTrue(): void {
        $_SERVER['SERVER_PORT'] = 443;
        $this->assertTrue(Request::isSSL());
    }

    public function testIsSSLExpectFalse(): void {
        $_SERVER['SERVER_PORT'] = 80;
        $this->assertFalse(Request::isSSL());
    }

    public function testGetPort(): void {
        $_SERVER['SERVER_PORT'] = 8080;
        $this->assertEquals(8080, Request::getPort());

        $_SERVER['SERVER_PORT'] = 443;
        $this->assertEquals(443, Request::getPort());
    }

    public function testGetProtocolExpectHttp(): void {
        $_SERVER['SERVER_PORT'] = 8080;
        $this->assertEquals('http://', Request::getProtocol());
    }

    public function testGetProtocolExpectHttps(): void {
        $_SERVER['SERVER_PORT'] = 443;
        $this->assertEquals('https://', Request::getProtocol());
    }

    public function testGetProtocolExpectHttpsWithPrefix(): void {
        $_SERVER['SERVER_PORT'] = 443;
        $this->assertEquals('https://www.ulicms.de', Request::getProtocol('www.ulicms.de'));
    }

    public function testGetIp(): void {
        $_SERVER['REMOTE_ADDR'] = '123.123.123.123';
        $this->assertEquals('123.123.123.123', Request::getIp());
    }

    public function testGetIpWithProxy(): void {
        $_SERVER['REMOTE_ADDR'] = '123.123.123.123';
        $_SERVER['X_FORWARDED'] = '111.111.111.111';

        $this->assertEquals('111.111.111.111', Request::getIp());
    }

    public function testSiteProtocolExpectHttp(): void {
        $_SERVER['SERVER_PORT'] = 80;
        ob_start();
        site_protocol();
        $this->assertEquals('http://', ob_get_clean());
    }

    public function testSiteProtocolExpectHttps(): void {
        $_SERVER['SERVER_PORT'] = 443;
        ob_start();
        site_protocol();
        $this->assertEquals('https://', ob_get_clean());
    }

    public function testIsSSLReturnsFalse(): void {
        $_SERVER['SERVER_PORT'] = 80;
        $this->assertFalse(is_ssl());
    }

    public function testIsSSLReturnsTrue(): void {
        $_SERVER['SERVER_PORT'] = 443;
        $this->assertTrue(is_ssl());
    }

    public function testIsHeaderSentReturnsTrue(): void {
        $this->assertTrue(
            Request::isHeaderSent(
                'Content-Type',
                [
                    'Content-Type: text/plain'
                ]
            )
        );
    }

    public function testIsHeaderSentReturnsFalse(): void {
        $this->assertFalse(Request::isHeaderSent('Foobar'));
    }
}
