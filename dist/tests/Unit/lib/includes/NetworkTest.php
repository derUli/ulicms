<?php

class NetworkTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        $_SERVER = [];
    }

    protected function tearDown(): void {
        $_SERVER = [];
    }

    public function testGetHostWithoutProxyReturnsHttpHost(): void {
        $_SERVER['HTTP_HOST'] = 'http.host';
        $_SERVER['SERVER_NAME'] = 'server.name';
        $this->assertEquals('http.host', get_host());
    }

    public function testGetHostWithoutProxyReturnsServerName(): void {
        $_SERVER['SERVER_NAME'] = 'server.name';
        $this->assertEquals('server.name', get_host());
    }

    public function testGetHostWithoutProxyReturnsServerAdress(): void {
        $_SERVER['SERVER_ADDR'] = '192.168.2.6';
        $this->assertEquals('192.168.2.6', get_host());
    }

    public function testGetHostWithProxyReturnsForwardedHost(): void {
        $_SERVER['HTTP_HOST'] = 'http.host';
        $_SERVER['SERVER_NAME'] = 'server.name';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'host1, host2,host3 ';
        $this->assertEquals('host3', get_host());
    }
}
