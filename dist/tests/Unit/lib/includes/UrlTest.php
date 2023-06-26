<?php

class UrlTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        chdir(ULICMS_ROOT);
    }

    public function testGetJqueryUrl(): void {
        $this->assertEquals('node_modules/jquery/dist/jquery.min.js', get_jquery_url());
    }

    public function testGetShortlink(): void {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = '443';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/foobar/foo';

        $pages = ContentFactory::getAll();

        $expected = '/?goid=' . $pages[0]->getId();
        $shortlink = get_shortlink($pages[0]->getId());

        $this->assertEquals(
            'https://example.org/foobar/?goid=1',
            $shortlink
        );
    }

    public function testGetCanonical(): void {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = '443';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/foobar/foo';

        $_GET['slug'] = 'hello_world';

        $this->assertEquals(
            'https://example.org/foobar/hello_world',
            get_canonical()
        );
    }

    public function testGetBaseFolderUrlWithoutFilename(): void {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/foobar/';

        $this->assertEquals('http://example.org/foobar', getBaseFolderURL());

        unset($_SERVER['SERVER_PROTOCOL'], $_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'], $_SERVER['REQUEST_URI']);

    }

    public function testGetCurrentURL(): void {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = '8080';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/foobar/foo?hello=world';

        $this->assertEquals('https://example.org:8080/foobar/foo?hello=world', getCurrentURL());

        unset($_SERVER['SERVER_PROTOCOL'], $_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'], $_SERVER['REQUEST_URI'], $_SERVER['HTTPS']);

    }

    public function testBuildSEOUrlWithoutAnythingNoPageSpecified(): void {
        unset($_GET['slug'], $_GET['html']);

        $this->assertEquals('./', buildSEOUrl());
    }

    public function testBuildSEOUrlWithoutAnything(): void {
        set_requested_pagename('hello_world');
        $this->assertEquals('hello_world', buildSEOUrl());
    }

    public function testBuildSEOUrlWithPage(): void {
        $this->assertEquals('foobar', buildSEOUrl('foobar'));
    }

    public function testBuildSEOUrlWithPageAndRedirection(): void {
        $this->assertEquals('#', buildSEOUrl('foobar', '#'));

        $this->assertEquals('https://google.com', buildSEOUrl('foobar', 'https://google.com'));
    }

    public function testBuildSEOUrlWithPageAndType(): void {
        $this->assertEquals(
            'foobar',
            buildSEOUrl('foobar', null)
        );
    }

    public function testBuildSEOUrlInAdminDir(): void {
        chdir(\App\Utils\Path::resolve('ULICMS_ROOT/admin'));

        $this->assertEquals(
            '../foobar',
            buildSEOUrl('foobar', null)
        );
    }
}
