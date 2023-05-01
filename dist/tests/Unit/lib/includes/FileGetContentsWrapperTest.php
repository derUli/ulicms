<?php

use App\Exceptions\CorruptDownloadException;
use App\Utils\CacheUtil;

class FileGetContentsWrapperTest extends \PHPUnit\Framework\TestCase {
    public const EXAMPLE_URL_OK = 'https://www.ulicms.de/robots.txt';

    public const EXAMPLE_URL_INVALID = 'http://www.google.de';

    public const EXAMPLE_HASH = '485b2c1ec3bc6b6fd93297ed4b1140b5';

    public const UNIQID_URL = 'http://test.ulicms.de/uniqid.php';

    public const USER_AGENT_URL = 'http://test.ulicms.de/useragent.php';

    protected function setUp(): void {
        CacheUtil::clearCache();
    }

    protected function tearDown(): void {
        CacheUtil::clearCache();
    }

    public function testFileGetContentsWrapperWithLocalPath(): void {
        $fileContent = file_get_contents_wrapper(
            Path::resolve(
                'ULICMS_ROOT/tests/fixtures/lorem_ipsum.txt'
            )
        );
        $this->assertStringContainsString('Lorem ipsum', $fileContent);
    }

    public function testDownloadUrlWithChecksumValid(): void {
        $this->assertTrue(is_string(file_get_contents_wrapper(self::EXAMPLE_URL_OK, true, self::EXAMPLE_HASH)));
    }

    public function testDownloadUrlWithChecksumInvalid(): void {
        try {
            file_get_contents_wrapper(self::EXAMPLE_URL_INVALID, true, self::EXAMPLE_HASH);
            $this->fail('Expected Exception has not been raised.');
        } catch (CorruptDownloadException $ex) {
            $this->assertEquals('Download of ' . self::EXAMPLE_URL_INVALID . ' - Checksum validation failed', $ex->getMessage());
        }
    }

    // curl_url_exists supports only http / https
    // and is used by url_exists if php curl module is installed
    public function testCurlUrlExistsReturnsTrue(): void {
        $this->assertTrue(curl_url_exists('http://example.org'));
        $this->assertTrue(curl_url_exists('https://www.ulicms.de/content/images/67cc042b9ee9eb28cdc81ae7d7420d8a.png'));
    }

    // curl_url_exists supports only http / https
    // and is used by url_exists if php curl module is installed
    public function testCurlUrlExistsReturnsFalse(): void {
        $this->assertFalse(curl_url_exists('http://www.gibtsnicht.ch/'));
        $this->assertFalse(curl_url_exists('https://www.ulicms.de/gibtsnicht.html'));
    }

    public function testFileGetContentsWrapperNoCache(): void {
        $first = file_get_contents_wrapper(self::UNIQID_URL, true, null);
        $second = file_get_contents_wrapper(self::UNIQID_URL, true, null);
        $this->assertNotEquals($second, $first);
    }

    public function testFileGetContentsWrapperCache(): void {
        $first = file_get_contents_wrapper(self::UNIQID_URL, false, null);
        $second = file_get_contents_wrapper(self::UNIQID_URL, false, null);
        $this->assertEquals($second, $first);
    }

    public function testFileGetContentsWrapperCacheAndChecksum(): void {
        $first = file_get_contents_wrapper(self::UNIQID_URL, false, null);
        $second = file_get_contents_wrapper(self::UNIQID_URL, false, md5($first));
        $this->assertEquals($second, $first);
    }
}
