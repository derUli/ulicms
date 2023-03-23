<?php

class UrlTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        chdir(ULICMS_ROOT);
    }

    public function testGetJqueryUrl()
    {
        $this->assertEquals("node_modules/jquery/dist/jquery.min.js", get_jquery_url());
    }

        public function testGetShortlink()
        {
            $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
            $_SERVER['SERVER_PORT'] = "443";
            $_SERVER['HTTPS'] = "on";
            $_SERVER['HTTP_HOST'] = "example.org";
            $_SERVER['REQUEST_URI'] = "/foobar/foo";

            $pages = ContentFactory::getAll();

            $expected = '/?goid=' . $pages[0]->getId();
            $shortlink = get_shortlink($pages[0]->getId());

            $this->assertEquals(
                "https://example.org/foobar/?goid=1",
                $shortlink
            );
        }

    public function testGetCanonical()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = "443";
        $_SERVER['HTTPS'] = "on";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/foo";

        $_GET["slug"] = "hello_world";

        $this->assertEquals(
            "https://example.org/foobar/hello_world",
            get_canonical()
        );
    }
     public function testGetBaseFolderUrlWithoutFilename()
     {
         $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
         $_SERVER['SERVER_PORT'] = "80";
         $_SERVER['HTTP_HOST'] = "example.org";
         $_SERVER['REQUEST_URI'] = "/foobar/";

         $this->assertEquals("http://example.org/foobar", getBaseFolderURL());

         unset($_SERVER['SERVER_PROTOCOL']);
         unset($_SERVER['HTTP_HOST']);
         unset($_SERVER['SERVER_PORT']);
         unset($_SERVER['REQUEST_URI']);
     }

    public function testGetCurrentURL()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = "8080";
        $_SERVER['HTTPS'] = "on";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/foo?hello=world";

        $this->assertEquals("https://example.org:8080/foobar/foo?hello=world", getCurrentURL());

        unset($_SERVER['SERVER_PROTOCOL']);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['REQUEST_URI']);
        unset($_SERVER['HTTPS']);
    }

     public function testBuildSEOUrlWithoutAnythingNoPageSpecified()
     {
         unset($_GET["slug"]);
         unset($_GET["html"]);

         $this->assertEquals("./", buildSEOUrl());
     }

    public function testBuildSEOUrlWithoutAnything()
    {
        set_requested_pagename("hello_world");
        $this->assertEquals("hello_world", buildSEOUrl());
    }

    public function testBuildSEOUrlWithPage()
    {
        $this->assertEquals("foobar", buildSEOUrl("foobar"));
    }

    public function testBuildSEOUrlWithPageAndRedirection()
    {
        $this->assertEquals("#", buildSEOUrl("foobar", "#"));

        $this->assertEquals("https://google.com", buildSEOUrl("foobar", "https://google.com"));
    }

    public function testBuildSEOUrlWithPageAndType()
    {
        $this->assertEquals(
            "foobar",
            buildSEOUrl("foobar", null)
        );
    }

    public function testBuildSEOUrlInAdminDir()
    {
        chdir(Path::resolve('ULICMS_ROOT/admin'));

        $this->assertEquals(
            "../foobar",
            buildSEOUrl("foobar", null)
        );
    }
}
