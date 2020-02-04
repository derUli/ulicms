<?php

class NetworkTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        unset($_SERVER["HTTP_HOST"]);
        unset($_SERVER["SERVER_NAME"]);
        unset($_SERVER["HTTP_X_FORWARDED_HOST"]);
    }

    public function testGetHostWithoutProxyReturnsHttpHost() {
        $_SERVER["HTTP_HOST"] = "http.host";
        $_SERVER["SERVER_NAME"] = "server.name";
        $this->assertEquals("http.host", get_host());
    }

    public function testGetHostWithoutProxyReturnsServerName() {
        $_SERVER["SERVER_NAME"] = "server.name";
        $this->assertEquals("server.name", get_host());
    }

    public function testGetHostWithoutProxyReturnsServerAdress() {
        $_SERVER["SERVER_ADDR"] = "192.168.2.6";
        $this->assertEquals("192.168.2.6", get_host());
    }

    public function testGetHostWithProxyReturnsForwardedHost() {
        $_SERVER["HTTP_HOST"] = "http.host";
        $_SERVER["SERVER_NAME"] = "server.name";
        $_SERVER["HTTP_X_FORWARDED_HOST"] = "host1, host2,host3 ";
        $this->assertEquals("host3", get_host());
    }

    public function testUliCMSMail() {
        $this->assertIsBool(
                ulicms_mail(
                        "nobody@mail.invali",
                        "My subject",
                        "Hello World"
                )
        );
    }

}
