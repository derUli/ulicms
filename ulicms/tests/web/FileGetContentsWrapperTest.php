<?php

use UliCMS\Exceptions\CorruptDownloadException;
use UliCMS\Utils\CacheUtil;

class FileGetContentsWrapperTest extends \PHPUnit\Framework\TestCase
{
    const EXAMPLE_URL_OK = "https://www.ulicms.de/robots.txt";
    const EXAMPLE_URL_INVALID = "http://www.google.de";
    const EXAMPLE_HASH = "485b2c1ec3bc6b6fd93297ed4b1140b5";
    const UNIQID_URL = "http://test.ulicms.de/uniqid.php";
    const USER_AGENT_URL = "http://test.ulicms.de/useragent.php";

    protected function setUp(): void
    {
        CacheUtil::clearCache();
    }

    protected function tearDown(): void
    {
        CacheUtil::clearCache();
    }

    public function testFileGetContentsWrapperWithLocalPath()
    {
        $fileContent = file_get_contents_wrapper(
            Path::resolve(
                "ULICMS_ROOT/tests/fixtures/lorem_ipsum.txt"
            )
        );
        $this->assertStringContainsString("Lorem ipsum", $fileContent);
    }

    public function testDownloadUrlWithChecksumValid()
    {
        $this->assertTrue(is_string(file_get_contents_wrapper(self::EXAMPLE_URL_OK, true, self::EXAMPLE_HASH)));
    }

    public function testDownloadUrlWithChecksumInvalid()
    {
        try {
            file_get_contents_wrapper(self::EXAMPLE_URL_INVALID, true, self::EXAMPLE_HASH);
            $this->fail("Expected Exception has not been raised.");
        } catch (CorruptDownloadException $ex) {
            $this->assertEquals("Download of " . self::EXAMPLE_URL_INVALID . " - Checksum validation failed", $ex->getMessage());
        }
    }

    public function testIsURLReturnsTrue()
    {
        $this->assertTrue(is_url("http://example.org"));
        $this->assertTrue(is_url("https://www.ulicms.de"));
        $this->assertTrue(is_url("ftp://ftp.hostserver.de/pub/OpenBSD/"));
    }

    public function testIsURLReturnsFalse()
    {
        $this->assertFalse(is_url("/var/www/html"));
        $this->assertFalse(is_url("C:\\xampp\\htdocs"));
        $this->assertFalse(is_url("http://"));
        $this->assertFalse(is_url("https://"));
        $this->assertFalse(is_url("ftp://"));
        $this->assertFalse(is_url(null));
    }

    public function testUrlExistsReturnsTrue()
    {
        $this->assertTrue(url_exists("http://example.org"));
        $this->assertTrue(url_exists("https://www.ulicms.de/content/images/67cc042b9ee9eb28cdc81ae7d7420d8a.png"));
    }

    public function testUrlExistsReturnsFalse()
    {
        $this->assertFalse(url_exists("http://www.gibtsnicht.ch/"));
        $this->assertFalse(url_exists("https://www.ulicms.de/gibtsnicht.html"));
    }

    // curl_url_exists supports only http / https
    // and is used by url_exists if php curl module is installed
    public function testCurlUrlExistsReturnsTrue()
    {
        $this->assertTrue(curl_url_exists("http://example.org"));
        $this->assertTrue(curl_url_exists("https://www.ulicms.de/content/images/67cc042b9ee9eb28cdc81ae7d7420d8a.png"));
    }

    // curl_url_exists supports only http / https
    // and is used by url_exists if php curl module is installed
    public function testCurlUrlExistsReturnsFalse()
    {
        $this->assertFalse(curl_url_exists("http://www.gibtsnicht.ch/"));
        $this->assertFalse(curl_url_exists("https://www.ulicms.de/gibtsnicht.html"));
    }

    public function testFileGetContentsCurlReturnsString()
    {
        $this->assertTrue(is_string(file_get_contents_curl("http://example.org")));
    }

    public function testFileGetContentsCurlReturnsNull()
    {
        $this->assertNull(file_get_contents_curl("http://www.gibtsnicht.ch"));
    }

    public function testFileGetContentsWrapperNoCache()
    {
        $first = file_get_contents_wrapper(self::UNIQID_URL, true, null);
        $second = file_get_contents_wrapper(self::UNIQID_URL, true, null);
        $this->assertNotEquals($second, $first);
    }

    public function testFileGetContentsWrapperCache()
    {
        $first = file_get_contents_wrapper(self::UNIQID_URL, false, null);
        $second = file_get_contents_wrapper(self::UNIQID_URL, false, null);
        $this->assertEquals($second, $first);
    }

    public function testFileGetContentsWrapperCacheAndChecksum()
    {
        $first = file_get_contents_wrapper(self::UNIQID_URL, false, null);
        $second = file_get_contents_wrapper(self::UNIQID_URL, false, md5($first));
        $this->assertEquals($second, $first);
    }

    public function testUserAgent()
    {
        $this->assertTrue(defined("ULICMS_USERAGENT"));
        $expectedUseragent = "UliCMS Release " . cms_version();
        $this->assertEquals($expectedUseragent, ULICMS_USERAGENT);
        $useragent = file_get_contents_wrapper(self::USER_AGENT_URL, true);
        $this->assertEquals($expectedUseragent, $useragent);
    }
}
